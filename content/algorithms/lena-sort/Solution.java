import java.io.*;
import java.math.*;
import java.security.*;
import java.text.*;
import java.util.*;
import java.util.concurrent.*;
import java.util.regex.*;

public class Solution {

    private static final Scanner scanner = new Scanner(System.in);

    public static void main(String[] args) {
        int q = scanner.nextInt();
        scanner.skip("(\r\n|[\n\r\u2028\u2029\u0085])?");

        for (int qItr = 0; qItr < q; qItr  ) {
            String[] lc = scanner.nextLine().split(" ");

            int l = Integer.parseInt(lc[0]);

            int c = Integer.parseInt(lc[1]);

            // Write Your Code Here
        }

        scanner.close();
    }
}
