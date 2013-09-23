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
#define MAX 100000
int color[MAX+10];
vector<int> adj[MAX+10];

bool check( int a, int b ){
    while( p[a] != -1 && p[a] != b )
            a = p[a];
    if( p[a] == b )
       return true;
    return false;
}

int main(){
    memset( p, -1, sizeof( p ) );
    int N, Q, a, b, admin;
    scanf( "%d %d %d", &N, &Q, &admin );
    for( int i=0; i< N - 1; i++ ){
        scanf( "%d %d", &a, &b );
        p[b] = a;
    }
    while( Q-- ){
        scanf( "%d %d", &a, &b );
        if( check( a, b ) )
            printf( "1\n" );
        else if( check( b, a ) )
            printf( "-1\n" );
        else
            printf( "0\n" );
    }
    return 0;
}
