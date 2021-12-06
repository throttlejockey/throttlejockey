<form method="post" action="https://api-3t.paypal.com/nvp">
API Username: <input type="text" name="USER" value="uk-3xxxx">  <br>
API Password: <input type="text" name="PWD" value="D25xxxxx">  <br>
API Signature: <input type="text" name="SIGNATURE" value="A7yWAlFxxxx">  <br>
Version: <input type="text" name="VERSION" value="204"> <br>
Paymentaction: <input type="text" name="PAYMENTREQUEST_0_PAYMENTACTION" value="sale"> <br>
Amount: <input type="text" name="PAYMENTREQUEST_0_AMT" value="7.50"> <br>
Item Amount: <input type="text" name="PAYMENTREQUEST_0_ITEMAMT" value="7.50"> <br>
Currency: <input type="text" name="PAYMENTREQUEST_0_CURRENCYCODE" value="USD"> <br>
ReturnURL: <input type="text" name="returnUrl" value="http://www.mywebsite.com/success.html"> <br>
CancelURL: <input type="text" name="cancelUrl" value="http://www.mywebsite.com/cancel.html"> <br>
SolutionType: <input type="text" name="solutiontype" value="Sole"> <br>
<br>
<input type="submit" name="METHOD" value="SetExpressCheckout"> <br>
</form>