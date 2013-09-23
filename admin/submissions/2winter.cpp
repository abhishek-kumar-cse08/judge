#include<cstdio>
#include<iostream>
#include<cstring>
#include<cmath>
#include<cstdlib>
#include<algorithm>
#include<string>
#include<iostream>
#include<map>
#include<vector>
#include<stack>
#include<queue>

using namespace std;

typedef pair< int, int > P;
typedef pair< P, int > TRI;
#define first( a ) ( (a).first.first )
#define second( a ) ( (a).first.second )
#define third( a ) ( (a).second )
#define ceil( a, b ) ( (a)/(b) + ( (a)*(b) >= 0 ? ( (a)%(b) != 0 ) : 0 ) )
#define floor( a, b ) ( (a)/(b) - ( (a)*(b) < 0 ? ( (a)%(b) != 0 ) : 0 ) )

#define INF 1000000000
#define MAX 200000
#define gc getchar

TRI edges[MAX+10];
int par[2][MAX+10], rank[2][MAX+10];
bool done[MAX+10];

bool compare( TRI a, TRI b ){
    return third( a ) < third( b );
}

int find_set( int *p, int i ){
    if( p[i] == 0 )
        return i;
    return find_set( p, p[i] );
}

void join( int *p, int *r, int i, int j ){
    int Pi = find_set( p, i ), Pj = find_set( p, j );
    if( r[ Pi ] >= r[ Pj ] ){
        p[ Pj ] = Pi;
        r[ Pi ] += r[ Pi ] == r[ Pj ];
    } else {
        p[ Pi ] = Pj;
    }
}

void scanint(int &x)
{
    register int c = gc();
    x = 0;
    int neg = 0;
    for(;((c<48 || c>57) && c != '-');c = gc());
    if(c=='-') {neg=1;c=gc();}
    for(;c>47 && c<58;c = gc()) {x = (x<<1) + (x<<3) + c - 48;}
    if(neg) x=-x;
}

int main(){
    //freopen( "out.txt", "r", stdin );

    int N, M, count = 0, Pa, Pb;
    scanint( N );
    scanint( M );
    //printf( "%d %d\n", N, M );

    for( int i=0; i<M; i++ ){
        scanint( first( edges[i] ) );
        scanint( second( edges[i] ) );
        scanint( third( edges[i] ) );
        first( edges[i] )++;
        second( edges[i] )++;
        //printf( "%d %d %d\n", first( edges[i] ), second( edges[i] ), third( edges[i] ) );
    }
    sort( edges, edges + M, compare );
    //for( int i=0; i<M; i++ )
      //  printf( "%d %d %d\n", first( edges[i] ), second( edges[i] ), third( edges[i] ) );
    for( int i=0; i<M && count < N - 1; i++ )
        if( ( Pa = find_set( par[0], first( edges[i] ) ) ) != ( Pb = find_set( par[0], second( edges[i] ) ) ) ){
            join( par[0], rank[0], Pa, Pb );
            done[i] = true;
            count++;
        }
    if( count !=  N - 1 ){
        printf( "NO\n" );
        return 0;
    }

    int start = 0, end = 0; count = 0;
    while( end < M && count < N - 1 ){
        start = end;
        while( end + 1 < M && third( edges[end+1] ) == third( edges[end] ) )
            end++;
        for( int i=start; i<=end; i++ )
            if( !done[i] && find_set( par[1], first( edges[i] ) ) != find_set( par[1], second( edges[i] ) ) ){
                printf( "NO\n" );
                return 0;
            }
        for( int i=start; i<=end; i++ )
            if( done[i] ){
                join( par[1], rank[1], first( edges[i] ), second( edges[i] ) );
                count++;
            }
        end++;
    }
    printf( "YES\n" );
    return 0;
}
