@if(env('LARAVEL_WEBSOCKETS_ENABLED'))
<script src="{{assetLink('js','laravel-echo')}}"></script>
<script src="{{assetLink('js','pusher')}}"></script>
<script type="text/javascript">
	let host = ["localhost", "127.0.0.1"].includes('{{env("LARAVEL_WEBSOCKETS_HOST")}}')?window.location.hostname:'{{env("LARAVEL_WEBSOCKETS_HOST")}}';
	let sslEnforce = '{{env("SOCKET_CLIENT_SSL_ENFORCEMENT")}}' == 1;
	window.Echo = new Echo({
	    broadcaster: 'pusher',
	    key: '{{env("MIX_PUSHER_APP_KEY")}}',
  			cluster: '{{env("MIX_PUSHER_APP_CLUSTER")}}',
  			wsHost: host,
  			wsPort:'{{env("LARAVEL_WEBSOCKETS_PORT")}}',
  			wssPort:'{{env("LARAVEL_WEBSOCKETS_PORT")}}',
  			disableStats: true,
  			forceTLS: sslEnforce
	});
</script>
@endif