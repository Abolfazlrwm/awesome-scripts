/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

#include <stdio.h>
#include <string.h>
#include <stdlib.h>

#define MAX_DIGITS 50

void reverse(char* str) {
    int len = strlen(str);
    for (int i = 0; i < len / 2; i++) {
        char temp = str[i];
        str[i] = str[len - i - 1];
        str[len - i - 1] = temp;
    }
}


void multiplyLargeNumbers(char* num1, char* num2, char* result) {
    int len1 = strlen(num1);
    int len2 = strlen(num2);
    int res[MAX_DIGITS * 2] = {0};

    reverse(num1);
    reverse(num2);

    for (int i = 0; i < len1; i++) {
        for (int j = 0; j < len2; j++) {
            res[i + j] += (num1[i] - '0') * (num2[j] - '0');
            if (res[i + j] >= 10) {
                res[i + j + 1] += res[i + j] / 10;
                res[i + j] %= 10;
            }
        }
    }

    int lenRes = len1 + len2;
    while (lenRes > 1 && res[lenRes - 1] == 0) {
        lenRes--;
    }

    for (int i = 0; i < lenRes; i++) {
        result[i] = res[i] + '0';
    }
    result[lenRes] = '\0';

    reverse(result);
    reverse(num1);
    reverse(num2);
}


int compareLargeNumbers(char* num1, char* num2) {
    int len1 = strlen(num1);
    int len2 = strlen(num2);

    if (len1 != len2) {
        return len1 > len2 ? 1 : -1;
    }

    for (int i = 0; i < len1; i++) {
        if (num1[i] != num2[i]) {
            return num1[i] > num2[i] ? 1 : -1;
        }
    }
    return 0;
}


void subtractLargeNumbers(char* num1, char* num2, char* result) {
    int len1 = strlen(num1);
    int len2 = strlen(num2);
    int borrow = 0;

    reverse(num1);
    reverse(num2);

    for (int i = 0; i < len1; i++) {
        int digit1 = num1[i] - '0';
        int digit2 = i < len2 ? num2[i] - '0' : 0;

        int diff = digit1 - digit2 - borrow;
        if (diff < 0) {
            diff += 10;
            borrow = 1;
        } else {
            borrow = 0;
        }
        result[i] = diff + '0';
    }

    int lenRes = len1;
    while (lenRes > 1 && result[lenRes - 1] == '0') {
        lenRes--;
    }
    result[lenRes] = '\0';

    reverse(result);
    reverse(num1);
    reverse(num2);
}


void divideLargeNumbers(char* dividend, char* divisor, char* quotient, char* remainder) {
    int lenDividend = strlen(dividend);
    char tempRemainder[MAX_DIGITS + 1] = {0};
    char tempQuotient[MAX_DIGITS * 2 + 1] = {0};

    int qIndex = 0;
    for (int i = 0; i < lenDividend; i++) {
        int lenTempRem = strlen(tempRemainder);
        tempRemainder[lenTempRem] = dividend[i];
        tempRemainder[lenTempRem + 1] = '\0';


        int startRem = 0;
        while (tempRemainder[startRem] == '0' && tempRemainder[startRem + 1] != '\0') {
            startRem++;
        }
        if (startRem > 0) {
            memmove(tempRemainder, &tempRemainder[startRem], strlen(&tempRemainder[startRem]) + 1);
        }

        int count = 0;
        while (compareLargeNumbers(tempRemainder, divisor) >= 0) {
            char temp[MAX_DIGITS + 1];
            subtractLargeNumbers(tempRemainder, divisor, temp);
            strcpy(tempRemainder, temp);
            count++;
        }
        tempQuotient[qIndex++] = count + '0';
    }

    tempQuotient[qIndex] = '\0';


    int startIndex = 0;
    while (tempQuotient[startIndex] == '0' && tempQuotient[startIndex + 1] != '\0') {
        startIndex++;
    }
    strcpy(quotient, &tempQuotient[startIndex]);


    strcpy(remainder, tempRemainder);
}

int main() {
    char num1[MAX_DIGITS + 1], num2[MAX_DIGITS + 1];
    char result[MAX_DIGITS * 2 + 1];
    char quotient[MAX_DIGITS * 2 + 1];
    char remainder[MAX_DIGITS + 1];

    printf("Enter first number for multiplication: ");
    scanf("%s", num1);
    printf("Enter second number for multiplication: ");
    scanf("%s", num2);

    multiplyLargeNumbers(num1, num2, result);
    printf("Multiplication Result: %s\n", result);


    printf("Enter dividend for division: ");
    scanf("%s", num1);
    printf("Enter divisor for division: ");
    scanf("%s", num2);

    divideLargeNumbers(num1, num2, quotient, remainder);
    printf("Quotient: %s\n", quotient);
    printf("Remainder: %s\n", remainder);

    return 0;
}
