#include <mysql.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <sys/types.h>
#include <signal.h>
#include <assert.h>

#define MAX 100

char itoa[MAX+5];
char Q[10*MAX+5];

void sleep_milli( int S ){
    usleep( 1000*S );
}

int is_terminated = 0, run_time;

void my_sig_handler(int signum)
{
    if (signum == SIGCHLD)
    {
        wait(&run_time);
        is_terminated = 1;
    }
}

int main() {
    struct sigaction sa;

    sa.sa_handler = my_sig_handler;
    sigemptyset(&sa.sa_mask);
    sa.sa_flags = 0;
    assert(sigaction(SIGCHLD, &sa, NULL) != -1);

    int id, time_limit;
    char *question, *language, *verdict;
    char file[MAX+10], exe[MAX+10];
    char input[10*MAX+10], answer[10*MAX+10], output[10*MAX+10];

    pid_t pid;

    MYSQL *conn;
    MYSQL_RES *res;
    MYSQL_ROW row;
    char *server = "localhost";
    char *user = "root";
    char *password = "sureshchandra";
    char *database = "coding";
    char *path_to_admin = "admin/";
    char *path_to_submissions = "admin/submissions/";
    int num_fields;
    conn = mysql_init(NULL);

    /* Connect to database */
    if (!mysql_real_connect(conn, server, user, password, database, 0, NULL, 0)) {
        fprintf(stderr, "%s\n", mysql_error(conn));
        exit(1);
    }

    while( 1 ){
        mysql_real_query( conn, "SELECT * FROM queue ORDER BY id ASC LIMIT 1", strlen( "SELECT * FROM queue ORDER BY id ASC LIMIT 1" ) );
        if( !( res = mysql_store_result( conn ) ) ){
            fprintf( stderr, "%s\n", "Unable to retrieve result from queue" );
            exit(1);
        }
        if( mysql_num_rows( res ) > 0 ){
            row = mysql_fetch_row( res );
            id = atoi( row[0] );
            question = row[2];
            language = row[3];
            sprintf( file, "%s%s", path_to_admin, row[5] );
            sprintf( exe, "%s%s", file, ".exe" );

            sprintf( Q, "%s%s%s", "SELECT * FROM questions WHERE code = \'", question, "\'" );
            mysql_real_query( conn, Q, strlen( Q ) );
            res = mysql_store_result( conn );
            if( mysql_num_rows( res ) != 1 ){
                verdict = "Question removed or corrupt.";
            }
            else{
                verdict = "Correct";
                row = mysql_fetch_row( res );
                time_limit = atoi( row[3] );
                if( strcmp( language, "c") == 0 )
                    sprintf( Q, "%s%s%s%s", "gcc ", file, " -o ", exe );
             	else if( strcmp( language, "cplusplus") == 0 )
                    sprintf( Q, "%s%s%s%s", "g++ ", file, " -o ", exe );
                else if( strcmp( language, "java" )  == 0 ){
                    sprintf( Q, "%s%s", "javac ", file );
                    sprintf( exe, "%d%s", id, "Main" );
                }

             	if( system( Q ) ){
                    verdict = "Compile Error";
             	}

             	sprintf( Q, "%s%s%s", "SELECT * FROM files WHERE code = \'", question, "\'" );
             	mysql_real_query( conn, Q, strlen( Q ) );
             	if( !( res = mysql_store_result( conn ) ) ){
                    fprintf( stderr, "%s%s%s\n", "Unable to retrieve Test Files for question \'", question, "\'"  );
                    exit(1);
                }
             	while( strcmp( verdict, "Correct" ) == 0 && ( row = mysql_fetch_row( res ) ) ){
             		sprintf( input, "%s%s", path_to_admin, row[1] );
             		sprintf( answer, "%s%s", path_to_admin, row[2] );
             		sprintf( output, "%s.%d", answer, id);
             		is_terminated = 0;
             		pid = fork();
             		if( pid < 0 ){
             			fprintf( stderr, "%s", "Error creating child process" );
             			exit(1);
             		} else if( pid == 0 ){
             			freopen( input, "r", stdin );
                        freopen( output, "w", stdout );
                        if( strcmp( language, "java" ) == 0 )
                            execl( "java", "java ", "-classpath ", path_to_submissions, exe, (char*)0 );
                        else{
                            execl( exe, exe, (char*)0 );
                        }
             		} else {
             			sleep( time_limit );
             			if( is_terminated == 0 ){
             				kill( pid, SIGKILL );
             				verdict = "Time Limit Exceeded";
             			}

                        if( run_time )
                            verdict = "Runtime Error";

             			if( strcmp( verdict, "Correct" ) == 0 ){
             				pid = fork();
             				if( pid < 0 ){
             					fprintf( stderr, "%s", "Error creating child process" );
             					exit(1);
             				} else if( pid == 0 ){
             					execl( "./matcher", "./matcher", answer, output, (char*)0 );
             				} else {
             					int status;
                                waitpid( pid, &status, 0 );
                                if( WIFEXITED(status) )
                                    status = WEXITSTATUS(status);
                                if( strcmp( verdict, "Correct" ) == 0 && status == 1 ){
                                    verdict = "Wrong Answer";
                                }
                                else if( strcmp( verdict, "Correct" ) == 0 && status == 2 ){
                                    verdict = "File Error";
                                }
                                else if( strcmp( verdict, "Correct" ) == 0 && status != 0 ){
                                    verdict = "Internal Server Error";
                                }
             				}
             			}
             		}
             	}
            }
            {
            printf( "Verdict = %s & id = %d\n", verdict, id );

            sprintf( Q, "%s%s%s%d", "UPDATE submissions SET verdict = \'", verdict , "\' WHERE id = ", id );
            mysql_real_query( conn, Q, strlen( Q ) );

            sprintf( Q, "%s%d", "DELETE FROM queue WHERE id = ", id );
            mysql_real_query( conn, Q, strlen( Q ) );

            sprintf( Q, "%s%s%s", "SELECT * FROM questions WHERE code = \'", question, "\' FOR UPDATE" );
            mysql_real_query( conn, Q, strlen( Q ) );
            res = mysql_store_result( conn );
            row = mysql_fetch_row( res );
            int total = atoi( row[10] ) + 1, correct = atoi( row[11] ) + ( strcmp( verdict, "Correct" ) == 0 );
            sprintf( Q, "%s%d%s%d%s%s%s", "UPDATE questions SET total = \'", total, "\', correct = \'", correct ,"\' WHERE code = \'", question, "\'" );
            mysql_real_query( conn, Q, strlen( Q ) );
            }
        } else {
            sleep_milli( 100 );
        }
    }

    mysql_free_result(res);
    mysql_close(conn);

    return 0;
}
