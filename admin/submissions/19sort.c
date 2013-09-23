#include<stdio.h>

int A[100+5];

int main(){
    int N, i;
    scanf( "%d", &N );
    for( i=1; i<=5; i++ )
        scanf( "%d", A + i );
    for( i=1; i<=5; i++ )
        printf( "%d ", i );
    printf( "\n" );
    return 0;
}
