#include<cstdio>
#include<cstdlib>
#include<cmath>
#include<algorithm>

using namespace std;

#define MAX 100000

int A[MAX+10];

int main(){
    int N;
    scanf( "%d", &N );
    for( int i=0; i<N; i++ )
        scanf( "%d", A + i );
    sort( A, A + N );
    for( int i=0; i<N; i++ )
        printf( "%d ", A[i] );
    printf( "\n" );
    return 0;
}
