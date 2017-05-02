[{assign var="data" value=$event|cat:'='|cat:$value}]
[{assign var="params" value=$data|base64_encode}]
<img src="[{oxgetseourl ident='/index.php?cl=makaira_connect_tracking' params="params="|cat:$params}]"/>
