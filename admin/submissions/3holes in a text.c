#include<string.h>
#include <stdio.h>
int main()
{
    int i,k,m;
    char s[100 + 5];
    scanf("%d",&i);
    while(i--)
    {
        int d=0;
        scanf("%s",s);
        k=strlen(s);
        for(m=0;m<k;m++)
        {
            char c=s[m];
            if(c=='A'||c=='P'||c=='R'||c=='O'||c=='D'||c=='Q')
            d++;
            else
            if(c=='B')
            d=d+2;
        }
        printf("%d\n",d);
    }
    return 0;
}

