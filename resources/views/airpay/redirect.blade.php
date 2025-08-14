<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Redirecting to Airpay</title>
    <script>
        function submitForm() {
            document.forms[0].submit();
        }
    </script>
</head>
<body onload="javascript:submitForm()">
    <center>
        <table width="500px">
            <tr>
                <td align="center" valign="middle">
                    Do Not Refresh or Press Back<br/>
                    Redirecting to Airpay...
                </td>
            </tr>
            <tr>
                <td align="center" valign="middle">
                    <form action="{{ $redirectData['url'] }}" method="post">
                        <input type="hidden" name="privatekey" value="{{ $redirectData['privatekey'] }}">
                        <input type="hidden" name="merchant_id" value="{{ $redirectData['merchant_id'] }}">
                        <input type="hidden" name="encdata" value="{{ $redirectData['encdata'] }}">
                        <input type="hidden" name="checksum" value="{{ $redirectData['checksum'] }}">
                    </form>
                </td>
            </tr>
        </table>
    </center>
</body>
</html>