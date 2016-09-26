<html>
<body><?php
/*
echo hash('sha256', 'abc');
echo hash('sha512', 'abc');
echo hash('md5', 'abc');
echo hash('sha1', 'abc');*/
print $json;
?>
<button onclick="hitax();">hit to go</button>
<script>
    function hitax() {
        window.open('ts.php', 'newwindow', 'toolbar=no, menubar=no, location=no, status=no');
        window.opener = null;
        window.open("", "_self");
        window.close();
    }
</script>
</body>
</html>