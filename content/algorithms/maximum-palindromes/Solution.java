import java.io.*;
import java.math.*;
import java.security.*;
import java.text.*;
import java.util.*;
import java.util.concurrent.*;
import java.util.regex.*;

class Result {

    /*
     * Complete the 'initialize' function below.
     *
     * The function accepts STRING s as parameter.
     */

    public static void initialize(String s) {
    // This function is called once before all queries.

    }

    /*
     * Complete the 'answerQuery' function below.
     *
     * The function is expected to return an INTEGER.
     * The function accepts following parameters:
     *  1. INTEGER l
     *  2. INTEGER r
     */

    public static int answerQuery(int l, int r) {
    // Return the answer for this query modulo 1000000007.

    }

}

public class Solution {
    public static void main(String[] args) throws IOException {
        BufferedReader bufferedReader = new BufferedReader(new InputStreamReader(System.in));
        BufferedWriter bufferedWriter = new BufferedWriter(new FileWriter(System.getenv("OUTPUT_PATH")));

        String s = bufferedReader.readLine();

        Result.initialize(s);

        int q = Integer.parseInt(bufferedReader.readLine().trim());

        for (int qItr = 0; qItr < q; qItr  ) {
            String[] firstMultipleInput = bufferedReader.readLine().replaceAll("\\s $", "").split(" ");

            int l = Integer.parseInt(firstMultipleInput[0]);

            int r = Integer.parseInt(firstMultipleInput[1]);

            int result = Result.answerQuery(l, r);

            bufferedWriter.write(String.valueOf(result));
            bufferedWriter.newLine();
        }

        bufferedReader.close();
        bufferedWriter.close();
    }
}
