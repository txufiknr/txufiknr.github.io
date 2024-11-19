<?php
$isLocalhost = in_array($_SERVER['HTTP_HOST'], ['localhost', '192.168.1.71']);
$browser = get_browser(null, true);
$isMobile = $browser['ismobiledevice'];

if (!$isLocalhost) header('Cache-Control: max-age=31536000'); // enable cache
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) { // enable gzip
  header('Content-Encoding: gzip');
  if (!ob_start("ob_gzhandler")) ob_start();
} else {
  ob_start();
}

define('KEY_UNSPLASH_CLIENT_ID', "dyzWO5hxYsulu9mUAfXykunuIJXg4871mUbxRQRFQQg");
define('URL_LINKEDIN', "https://www.linkedin.com/in/taufik-nur-rahmanda/");
define('URL_INSTAGRAM', "https://www.instagram.com/txufiknr");
define('URL_TWITTER', "https://x.com/txufiknr");
define('URL_GITHUB', "https://github.com/topex-psy");
define('URL_PLAYSTORE', "https://play.google.com/store/apps/dev?id=7554821831783338570");
define('URL_WEBSITE', 'https://birvee.com/portfolio');
define('URL_BASE', URL_WEBSITE.'/');
define('LANGUAGE_OPTIONS', ['en', 'id']);
define('LANGUAGE_DEFAULT', 'en');
define('PATH_PHOTO', "assets/images/photo.webp");
define('PATH_SCRIPT', "assets/js/script".($isLocalhost?"":".min").".js");
define('PATH_STYLE', "assets/css/base".($isLocalhost?"":".min").".css");
define('PATH_STYLE_TABLET', "assets/css/tablet".($isLocalhost?"":".min").".css");
define('PATH_STYLE_DESKTOP', "assets/css/desktop".($isLocalhost?"":".min").".css");
define('BIO_FULL_NAME', "Taufik Nur Rahmanda, S.Kom");
define('BIO_FIRST_NAME', "Taufik");
define('JOB_FIRST_YEAR', 2016);
define('JOB_LANGUAGES', 10);
define('JOB_PROJECTS', 50);

$hostURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
$hostURL.= "://".$_SERVER['HTTP_HOST'];
$pathURL = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
$baseURL = $hostURL.$pathURL;

include('functions.php');

$req_uri = $_SERVER['REQUEST_URI'];
$lang = $_COOKIE['language'] ?? LANGUAGE_DEFAULT;
if (!in_array($lang, LANGUAGE_OPTIONS)) $lang = LANGUAGE_DEFAULT;

$path_withlang = str_replace_first($pathURL, '', $req_uri);
$path_splitted = explode('/', $path_withlang);
if (in_array($path_splitted[0], LANGUAGE_OPTIONS)) {
  $langURL = $path_splitted[0];
  $page = count($path_splitted) > 1 ? $path_splitted[1] : '';
  
  // language code in url different with language code in the cookie
  if ($lang != $langURL) {
    $lang = $langURL;
    // set the cookie accordingly
    // setcookie('language', $lang, time() + (86400 * 365), "/");
    // $_COOKIE['language'] = $lang;
  }

  // redirect /en to default non-hreflang url
  if ($lang == LANGUAGE_DEFAULT) {
    header('Location: '.$baseURL.$page, TRUE, 301);
    exit;
  }
} else {
  $langURL = '';
  $page = $path_splitted[0];

  // redirect non-english page to hreflang url
  if ($lang != LANGUAGE_DEFAULT) {
    header('Location: '.$baseURL.$lang.'/'.$page, TRUE, 301);
    exit;
  }
}

$tr = json_decode(file_get_contents("assets/lang/$lang.json"), true);
$page = $page == '' ? 'home' : $page;
$href = $page == 'home' ? './' : $page;
$title = $tr['meta_title_'.$page] ?? $tr['meta_title'];
$description = $tr['meta_description_'.$page] ?? $tr['meta_description'];
$keyword = $tr['meta_keyword_'.$page] ?? $tr['meta_keyword'];
$pageTitle = BIO_FULL_NAME.' | '.$title;
$pageURL = $page == 'home' ? '' : '/'.$page;
$canonical = URL_WEBSITE.($langURL ? '/'.$langURL : '').$pageURL;
$years = date('Y') - JOB_FIRST_YEAR;
$isMobile = $isMobile || is_mobile();

$pathScript = PATH_SCRIPT.'?'.filemtime(PATH_SCRIPT);
$pathStyle = PATH_STYLE.'?'.filemtime(PATH_STYLE);
$pathStyleTablet = PATH_STYLE_TABLET.'?'.filemtime(PATH_STYLE_TABLET);
$pathStyleDesktop = PATH_STYLE_DESKTOP.'?'.filemtime(PATH_STYLE_DESKTOP);

// if ($page == 'home') {
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.unsplash.com/photos/random?query=tech&client_id='.KEY_UNSPLASH_CLIENT_ID);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$result = json_decode($response, true);
curl_close($ch);

$hero = $result['urls']['regular']; // full
?>

<!DOCTYPE html>
<html lang="<?=$lang?>" prefix="og: http://ogp.me/ns#">
<head>
	<meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

	<title><?=$pageTitle?></title>

	<meta name="description" content="<?=$description?>">
  <meta name="keywords" content="<?=$keyword?>">
	<meta name="author" content="<?=BIO_FULL_NAME?>">
	<meta name="copyright" content="<?=BIO_FULL_NAME?>">

  <meta property="og:locale<?=$lang=='en'?'':':alternate'?>" content="en_US">
  <meta property="og:locale<?=$lang=='id'?'':':alternate'?>" content="id_ID" />
	<meta property="og:type" content="website">
	<meta property="og:title" content="<?=$pageTitle?>">
	<meta property="og:description" content="<?=$description?>">
	<meta property="og:url" content="<?=$canonical?>">
	<meta property="og:image" content="<?=URL_BASE.PATH_PHOTO?>">
	<meta property="og:image:alt" content="<?=BIO_FIRST_NAME?>'s photo">
	<meta property="og:site_name" content="<?=BIO_FULL_NAME?>">

  <meta name="twitter:url" content="<?=$canonical?>"/>
  <meta name="twitter:title" content="<?=$pageTitle?>"/>
  <meta name="twitter:description" content="<?=$description?>"/>
  <meta name="twitter:image" content="<?=URL_BASE.PATH_PHOTO?>"/>
  <meta name="twitter:site" content="@txufiknr">
  <meta name="twitter:card" content="summary">

  <base href="<?=$baseURL?>">

	<!-- Favicons -->
	<link href="favicon.png" rel="icon">
  <link href="apple-touch-icon.png" rel="apple-touch-icon">
  
  <!-- Canonical -->
  <link rel="alternate" href="<?=URL_WEBSITE.$pageURL?>" hreflang="en">
  <link rel="alternate" href="<?=URL_BASE.'id'.$pageURL?>" hreflang="id">
  <link rel="canonical" href="<?=$canonical?>">

  <!-- Pre-connects -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="dns-prefetch" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <!-- Pre-loads -->
  <link rel="preload" href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300;400;600;700&display=block" as="style" fetchpriority="high">
  <link rel="preload" href="<?=PATH_PHOTO?>" as="image" fetchpriority="high">
  <link rel="preload" href="<?=$pathScript?>" as="script">

	<!-- Styles -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300;400;600;700&display=block">
  <link rel="stylesheet" href="<?=$pathStyle?>" media="screen">
	<link rel="stylesheet" href="<?=$pathStyleTablet?>" media="screen and (min-width: 768px)"<?=$isMobile?' disabled':''?>>
	<link rel="stylesheet" href="<?=$pathStyleDesktop?>" media="screen and (min-width: 992px)"<?=$isMobile?' disabled':''?>>
	<?php if (isset($hero)) { ?>
	<style>#hero { background-image: url(<?=$hero?>); }</style>
	<?php } ?>
	
	<!-- Scripts -->
	<script src="assets/vendor/counterup2/counterup2.js" defer></script>
  <script src="assets/vendor/particles/particles.min.js" defer></script>
	<script src="assets/vendor/typewrite/typewrite.js" defer></script>
  <script src="<?=$pathScript?>" defer></script>
</head>

<?php ob_flush();?>

<body data-page="<?=$page?>" data-href="<?=$href?>">
	<div id="scroll-watcher"></div>
	<div id="scroll-to-top" role="button" aria-label="Scroll to top"><img alt="Scroll to top" src="<?=PATH_PHOTO?>"></div>

	<section id="hero">
		<img alt="<?=BIO_FIRST_NAME?>'s photo" src="<?=PATH_PHOTO?>" fetchpriority="high">
		<div id="particles-js"></div>

		<div class="container">
			<h1><span>Taufik</span> Nur Rahmanda, S.Kom</h1>
			<p class="typewrite" data-period="4000" data-type='["<?=$tr['intro_1']?>", "<?=$tr['intro_2']?>", "<?=$tr['intro_3']?>"]'>
				<span class="wrap"></span>
			</p>
			<p><?="$tr[bio_1] $years+ $tr[bio_2]"?><span class="d-tablet-inline"> <?=$tr['bio_3']?></span></p>
			<?php include('components/socials.php'); ?>
		</div>

		<img alt="Scroll down" src="assets/icons/chevron-compact-down.svg" id="scroll-down">

		<div id="lang">
			<button class="btn" type="button" aria-label="Select language"><?=strtoupper($lang)?></button>
			<div class="menu">
				<a href="<?=$baseURL?>" data-value="en">English</a>
				<a href="<?=$baseURL?>" data-value="id">Indonesian</a>
			</div>
		</div>
	</section>

	<section id="skills">
		<div class="container">

			<div class="vendors">
				<div>
					<img loading="lazy" alt="react" src="assets/icons/vendors/react-svgrepo-com.svg">
					<img loading="lazy" alt="vue" src="assets/icons/vendors/vue-svgrepo-com.svg">
					<img loading="lazy" alt="angular" src="assets/icons/vendors/angular-icon-svgrepo-com.svg">
					<img loading="lazy" alt="nextjs" src="assets/icons/vendors/nextjs-icon-svgrepo-com.svg" style="filter: invert(.6);">
					<img loading="lazy" alt="flutter" src="assets/icons/vendors/flutter-svgrepo-com.svg">
					<img loading="lazy" alt="dart" src="assets/icons/vendors/dart-svgrepo-com.svg">
					<img loading="lazy" alt="android" src="assets/icons/vendors/android-icon-svgrepo-com.svg" style="filter: grayscale(1) brightness(1);">
					<img loading="lazy" alt="apple" src="assets/icons/vendors/apple-svgrepo-com.svg" style="filter: invert(.8);">
					<img loading="lazy" alt="firebase" src="assets/icons/vendors/firebase-svgrepo-com.svg" style="filter: grayscale(1) brightness(1);">
					<img loading="lazy" alt="kotlin" src="assets/icons/vendors/kotlin-svgrepo-com.svg">
					<img loading="lazy" alt="php" src="assets/icons/vendors/php-alt-svgrepo-com.svg" style="filter: invert();">
					<img loading="lazy" alt="codeigniter" src="assets/icons/vendors/codeigniter-svgrepo-com.svg" style="filter: grayscale(1) brightness(2);">
					<img loading="lazy" alt="mysql" src="assets/icons/vendors/mysql-svgrepo-com.svg" style="filter: grayscale(1) brightness(3);">
					<img loading="lazy" alt="bootstrap" src="assets/icons/vendors/bootstrap-svgrepo-com.svg" style="filter: grayscale(1) brightness(1.5);">
					<img loading="lazy" alt="css" src="assets/icons/vendors/css-3-official-svgrepo-com.svg" style="filter: grayscale(1) brightness(2);">
					<img loading="lazy" alt="es6" src="assets/icons/vendors/es6-svgrepo-com.svg">
					<img loading="lazy" alt="typescript" src="assets/icons/vendors/typescript-icon-svgrepo-com.svg">
					<img loading="lazy" alt="nodejs" src="assets/icons/vendors/nodejs-icon-svgrepo-com.svg">
					<img loading="lazy" alt="python" src="assets/icons/vendors/python-svgrepo-com.svg">
					<img loading="lazy" alt="unity" src="assets/icons/vendors/unity-svgrepo-com.svg" style="filter: invert(.7);">

					<img loading="lazy" alt="react" src="assets/icons/vendors/react-svgrepo-com.svg">
					<img loading="lazy" alt="vue" src="assets/icons/vendors/vue-svgrepo-com.svg">
					<img loading="lazy" alt="angular" src="assets/icons/vendors/angular-icon-svgrepo-com.svg">
					<img loading="lazy" alt="nextjs" src="assets/icons/vendors/nextjs-icon-svgrepo-com.svg" style="filter: invert(.6);">
					<img loading="lazy" alt="flutter" src="assets/icons/vendors/flutter-svgrepo-com.svg">
					<img loading="lazy" alt="dart" src="assets/icons/vendors/dart-svgrepo-com.svg">
				</div>
			</div>

			<div class="testimonies d-desktop">
				<div class="testimony"><q><?=$tr['testimony_1']?></q><p>&mdash; John, Leader at Tech Company</p><img loading="lazy" alt="Testimony photo 1" src="assets/images/dummy/male.webp"></div>
				<div class="testimony"><q><?=$tr['testimony_2']?></q><p>&mdash; Edwin, Leader at Tech Company</p><img loading="lazy" alt="Testimony photo 2" src="assets/images/dummy/male.webp"></div>
				<div class="testimony"><q><?=$tr['testimony_3']?></q><p>&mdash; Vera, Leader at Tech Company</p><img loading="lazy" alt="Testimony photo 3" src="assets/images/dummy/female.webp"></div>
				<div class="testimony"><q><?=$tr['testimony_4']?></q><p>&mdash; Melly, Leader at Tech Company</p><img loading="lazy" alt="Testimony photo 4" src="assets/images/dummy/female.webp"></div>
				<div class="testimony"><q><?=$tr['testimony_5']?></q><p>&mdash; Vina, Leader at Tech Company</p><img loading="lazy" alt="Testimony photo 5" src="assets/images/dummy/female.webp"></div>
			</div>

			<div class="title-container">
				<div class="title">
					<h1><?=$tr['my_skills']?></h1>
					<pre>&gt;<span class="blink">_</span></pre>
				</div>
				<div class="stats">
					<div class="stat">
						<p class="counter"><?=number_format(JOB_PROJECTS)?>+</p>
						<p><?=$tr['projects']?></p>
					</div>
					<div class="stat d-desktop">
						<p class="counter"><?=number_format(JOB_LANGUAGES)?>+</p>
						<p><?=$tr['languages']?></p>
					</div>
					<div class="stat">
						<p class="counter"><?=$years?>+</p>
						<p><?=$tr['years']?></p>
					</div>
				</div>
			</div>
			<p class="texts">
				PHP<br>
				HTML<br>
				CSS<br>
				Bootstrap<br>
				jQuery<br>
				CodeIgniter<br>
				MySQL<br>
				MariaDB<br>
				Vue<br>
				React.js<br>
				Next.js<br>
				Angular<br>
				JavaScript<br>
				TypeScript<br>
				ECMAScript<br>
				Java<br>
				Kotlin<br>
				Flutter<br>
				Dart<br>
				XCode<br>
				Android Studio<br>
				Git<br>
				Firebase<br>
				Node.js<br>
				Adobe Photoshop<br>
				MAGIX Vegas Pro<br>
				Cocos Creator<br>
				Unity<br>
			</p>
		</div>
	</section>

	<section id="portfolio">
		<h1 class="portfolio-title">Portfolio</h1>
		<div id="particles-js2"></div>
		<div id="singularity">
			<div id="blackhole"></div>
			<div id="radiation">
				<i></i>
				<i></i>
				<i></i>
				<i></i>
			</div>
		</div>
		<div class="title">
			<h1><?=$tr['What_i_have']?><br><span class="typewrite" data-period="4000" data-type='["<?=$tr['created']?>", "<?=$tr['built']?>", "<?=$tr['published']?>"]'><span class="wrap"></span></span></h1>
			<h2><?=$tr['follow_me']?></h2>
		</div>
		<div class="item birvee">
			<img loading="lazy" alt="Birvee app screenshot" src="assets/images/portfolio/birvee.webp">
			<h2>Birvee</h2>
			<div>
				<p><?=$tr['portfolio_birvee']?></p>
				<img loading="lazy" alt="apple" src="assets/icons/vendors/apple-svgrepo-com.svg">
				<a href="https://play.google.com/store/apps/details?id=com.social.birv" target="_blank" title="Download on Play Store">
					<img loading="lazy" alt="android" src="assets/icons/vendors/android-icon-svgrepo-com.svg">
				</a>
			</div>
		</div>
		<div class="item foom text-right">
			<img loading="lazy" alt="Foom Now app screenshot" src="assets/images/portfolio/foom.webp">
			<h2>Foom Now</h2>
			<div>
				<p><?=$tr['portfolio_foom']?></p>
				<a href="https://apps.apple.com/id/app/foom-now/id6470125134" target="_blank" title="Download on App Store">
					<img loading="lazy" alt="apple" src="assets/icons/vendors/apple-svgrepo-com.svg">
				</a>
				<img loading="lazy" alt="android" src="assets/icons/vendors/android-icon-svgrepo-com.svg">
			</div>
		</div>
		<div class="item leapverse">
			<img loading="lazy" alt="Leapverse app screenshot" src="assets/images/portfolio/leapverse.webp">
			<h2>Leapverse</h2>
			<div>
				<p><?=$tr['portfolio_leap']?></p>
				<a href="https://apps.apple.com/id/app/leapverse/id1610709042" target="_blank" title="Download on App Store">
					<img loading="lazy" alt="apple" src="assets/icons/vendors/apple-svgrepo-com.svg">
				</a>
				<a href="https://play.google.com/store/apps/details?id=com.leap.leapverse" target="_blank" title="Download on Play Store">
					<img loading="lazy" alt="android" src="assets/icons/vendors/android-icon-svgrepo-com.svg">
				</a>
				<a href="https://leapverse.leapsurabaya.sch.id/" target="_blank" title="Visit website">
					<img loading="lazy" alt="android" src="assets/icons/web-icon.svg">
				</a>
			</div>
		</div>
		<div class="item buangduit text-right">
			<img loading="lazy" alt="BuangDuit app screenshot" src="assets/images/portfolio/buangduit.webp">
			<h2>BuangDuit</h2>
			<div>
				<p><?=$tr['portfolio_buangduit']?></p>
				<img loading="lazy" alt="android" src="assets/icons/vendors/android-icon-svgrepo-com.svg">
			</div>
		</div>
		<?php include('components/socials.php'); ?>
	</section>

	<footer>&copy;<?=date('Y')?> <?=BIO_FULL_NAME?></footer>

	<?php if (empty($_COOKIE['language'])) { ?>
	<div id="cookie-alert">
		<p><?=$tr['cookie']?></p>
	</div>
	<?php } ?>

</body>
</html>