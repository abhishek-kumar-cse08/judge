#include<stdio.h>
#include<stdlib.h>
#include<string.h>
#include<ctype.h>

int main( int argc, char *argv[] ){
	if( argc != 3 ){
		printf( "-3" );;
	}
	FILE *f[2]; int i;
	for( i=0; i<2; i++ ){
		f[i] = fopen( argv[i+1], "r" );
		if( !f[i] ){
			printf( "-2" );
		}
	}
	
	char c[2];
	do{
		while( ( c[0] = fgetc( f[0] ) ) != EOF && isspace( c[0] ) )
			;
		while( ( c[1] = fgetc( f[1] ) ) != EOF && isspace( c[1] ) )
			;
		if( c[0] != EOF && c[1] != EOF && c[0] != c[1] ){
			printf( "-1" );
		}
	} while( c[0] != EOF && c[1] != EOF );
	
	if( c[0] != EOF || c[1] != EOF ){
		printf( "-1" );
	}
	printf( "0" );
	return 0;
}
