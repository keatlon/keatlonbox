<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {

    FB.init({
      appId      : '<?= conf::$conf['facebook']['id'] ?>', // App ID
      status     : true,
      cookie     : true,
      oauth      : true,
      xfbml      : true
    });
  };
  (function(d){
             var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
             js = d.createElement('script'); js.id = id; js.async = true;
             js.src = "//connect.facebook.net/en_US/all.js";
             d.getElementsByTagName('head')[0].appendChild(js);
  }(document));

</script>