<?php
ob_start();
$hs = $_SERVER['HTTP_HOST'];
$ip = $_SERVER['REMOTE_ADDR'];
$ua = $_SERVER['HTTP_USER_AGENT'];
$rf = $_SERVER['HTTP_REFERER'] ?? '';
$ur = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')?'https':'http').'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];;

$mysqli = $hs == 'localhost'
	? new mysqli($hs, "root", "", "taufiknur")
	: new mysqli($hs, "u8846056_root", "aaaAAA123", "u8846056_taufiknur");

if ($mysqli->connect_error) {
	echo "Database connection failed: ".$mysqli->connect_error." (".$mysqli->connect_errno.")".PHP_EOL;
  exit;
}

if ($mysqli->query("INSERT INTO web_visitor (IP,USER_AGENT,REFERER,URI) VALUES ('$ip','$ua','$rf','$ur') ON DUPLICATE KEY UPDATE VISIT_COUNT=VISIT_COUNT+1") === TRUE) {
  echo "Welcome to the other side!".PHP_EOL;
} else {
	echo $mysqli->error.PHP_EOL;
}

$total_visitor_unique = 0;
$total_visitor_count  = 0;
if ($result = $mysqli->query("SELECT * FROM web_visitor")) {
	$total_visitor_unique = $result->num_rows;
	// $result_array = $result->fetch_all(MYSQLI_ASSOC);
	// print_r($result_array);
	while ($row = $result->fetch_assoc()) {
		$total_visitor_count += $row['VISIT_COUNT'];
  }
	$result->free_result();
}

$mysqli->close();
$output = ob_get_contents();
ob_end_clean();
?>

<!DOCTYPE html>
<html prefix="og: http://ogp.me/ns#" lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<title>TaufikNur Production</title>

	<meta property="og:title" content="TaufikNur Production" />
	<meta property="og:description" content="new Universe.initialize(content: Things.hapiness).then(shallowMe);" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="https://www.taufiknur.com" />
	<meta property="og:image" content="https://www.taufiknur.com/assets/images/image.png" />
	<meta property="fb:app_id" content="602167867223943" />

	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Oxygen+Mono&display=swap">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Proxima+Nova&display=swap">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">
	<link rel="stylesheet" href="assets/css/nav.css">
	<link rel="stylesheet" href="assets/css/style.css">
	<link rel="stylesheet" href="assets/css/typewrite.css">

	<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
</head>
<body>
	<div id="wrap-texture" style="opacity:0.3">
		<div id="canvas"></div>
		<div class="plane">
			<?php
			$tag = 'dark';
			for ($i = 0; $i < 4; $i++) {
				// echo '<img class="texture" crossorigin="anonymous" src="https://source.unsplash.com/1600x900/?'.$tag.'&r='.$i.time().'" />';
			}
			?>
		</div>
	</div>

	<nav class="nav-main">
		<ul>
			<li><a href="https://lnk.bio/ArjF" class="first-menu">Bio</a><span class="second-menu">Bio</span></li>
			<li><a href="https://www.wattpad.com/TaufikNurRahmanda" class="first-menu">Cerpen/Novel</a><span class="second-menu">Cerpen/Novel</span></li>
			<li><a href="https://www.youtube.com/c/TopExSharingan" class="first-menu">Video</a><span class="second-menu">Video</span></li>
			<li><a href="https://play.google.com/store/apps/dev?id=7554821831783338570" class="first-menu">Apps</a><span class="second-menu">Apps</span></li>
		</ul>
	</nav>
	
	<div id="content">
		<div style="position: relative;width: calc(30vh + 200px);">
			<div class="content-buttons" style="position: absolute;left: 0;bottom: 0;z-index: 1;">
				<a href="https://www.facebook.com/TopEx.Divine" target="_blank" style="margin-right: -3rem"><img src="assets/images/fb.png"></a>
				<a href="https://www.youtube.com/c/TopExSharingan" target="_blank"><img src="assets/images/yt.png"></a>
				<a href="https://www.wattpad.com/TaufikNurRahmanda" target="_blank" style="margin-right: -2rem"><img src="assets/images/wp.png"></a>
			</div>
			<div class="wheel-container">
				<img class="wheel" src="assets/images/wheel3.png">
				<img class="wheel-reverse" src="assets/images/wheel.png">
				<img id="content-canvas" src="https://avatars3.githubusercontent.com/u/14989865?s=460&v=4">
			</div>
			<div class="content-buttons" style="position: absolute;right: 0;bottom: -1rem;z-index: 1;">
				<a href="mejatamu" target="_blank" style="margin-bottom: .5rem"><img src="assets/images/projects/mejatamu.png" style="width: 60px"></a>
				<a href="laku" target="_blank" style="margin: 0 0 1rem 3rem;"><img src="assets/images/projects/laku.png" style="width: 60px"></a>
				<a href="buangduit" target="_blank" style="margin-right: 1rem"><img src="assets/images/projects/buangduit.png"></a>
			</div>
		</div>
		<h1>Taufik Nur Rahmanda, S.Kom.</h1>
		<h6>
			Universe.<!--
			--><span class="typewrite" data-period="4000" data-type='[
				"alter(contents: [Things.hapiness]).then(shallowMe);",
				"order((universe) => universe.inhabitants.has(you));",
				"destroy();"
			]'>
				<span class="wrap"></span>
			</span>
		</h6>
		<h5>Anda adalah pengunjung ke: <?=number_format($total_visitor_count)?></h5>
		<h5>Hak cipta &copy;<?=date('Y')?> Taufik Nur Rahmanda</h5>
    <i id="btn-get-started" class="bi bi-chevron-compact-down"></i>
	</div>
	
	<audio autoplay loop>
		<source src="assets/sound/A_Great_Darkness_Approaches_Can_You_Feel_It.mp3" type="audio/mpeg">
	</audio>
	
	<div id="page-loader">
		<span>Menembus portal ...</span>
	</div>
	
	<!-- bg -->
	<script src='https://cdnjs.cloudflare.com/ajax/libs/three.js/r71/three.min.js'></script>
	<script src='https://cdnjs.cloudflare.com/ajax/libs/stats.js/r11/Stats.min.js'></script>

	<!-- nav -->
	<script src='https://www.curtainsjs.com/build/curtains.min.js'></script>
	<script src='https://cdnjs.cloudflare.com/ajax/libs/gsap/2.1.3/TweenMax.min.js'></script>
	
	<script	src="assets/js/bg.js"></script>
	<script src="assets/js/nav.js"></script>
	<script src="assets/js/typewrite.js"></script>
	
	<script>
	$(() => {
		$('.nav-main').on('click', 'a[href]', function(e) {
			// e.preventDefault();
			console.log($(this).attr('href'));
		});
	});
	
	function universeDebugStart() {
		console.log('universeDebugStart();');
		var elements = document.getElementsByClassName('typewrite');
		for (var i=0; i<elements.length; i++) {
			var toRotate = elements[i].getAttribute('data-type');
			var period = elements[i].getAttribute('data-period');
			if (toRotate) {
				new TxtType(elements[i], JSON.parse(toRotate), period);
			}
		}
		// INJECT CSS
		// var css = document.createElement("style");
		// css.type = "text/css";
		// css.innerHTML = ".typewrite > .wrap { border-right: 0.08em solid #fff}";
		// document.body.appendChild(css);
		
		revelationDone();

		$('#content, .nav-main').css('pointer-events', 'auto').fadeTo(1000, 1);
		$('#page-loader').css('pointer-events', 'none').stop().fadeOut(5000, function() {
			$('#page-loader').remove();
		});
	}
	
	function revelationDone() {
		if (revealed) return;
		console.log('revelationDone();');
		revealed = true
		clearInterval(revelationInterval);
	}
	
	var obscured = 600;
	var revealed = false;
	var revelationInterval = setInterval(() => {
		obscured--;
		if (obscured <= 0) revelationDone();
		$('#page-loader').css('background','rgba(0,0,0,'+(obscured / 600)+')');
	}, 10);

	if (window.addEventListener) { // W3C standard
		window.addEventListener('load', universeDebugStart, false); // NB **not** 'onload'
	} else if (window.attachEvent) { // Microsoft
		window.attachEvent('onload', universeDebugStart);
	}
	</script>
</body>
</html>