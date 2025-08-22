<?php
// Start Gzip compression
if (extension_loaded('zlib') && !ini_get('zlib.output_compression') && !ob_get_level()) {
  ob_start('ob_gzhandler');
}

// Detect environments
$isLocalhost = in_array($_SERVER['HTTP_HOST'], ['localhost', '192.168.1.71']) || in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);
// $isMobile = isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/mobile|android|iphone|ipod|ipad|blackberry|webos|hpwos|opera mini|windows phone/i', $_SERVER['HTTP_USER_AGENT']);

// Define important file paths
define('PATH_SCRIPT', "assets/js/script.i18n".($isLocalhost?"":".min").".js");
define('PATH_STYLE', "assets/css/main".($isLocalhost?"":".min").".css");
// define('PATH_STYLE', "assets/css/base".($isLocalhost?"":".min").".css");
// define('PATH_STYLE_TABLET', "assets/css/tablet".($isLocalhost?"":".min").".css");
// define('PATH_STYLE_DESKTOP', "assets/css/desktop".($isLocalhost?"":".min").".css");

// Timestamp of important file change
$filesToCheck = [
  __FILE__,
  __DIR__.'/'.PATH_STYLE,
  __DIR__.'/'.PATH_SCRIPT,
];
// if ($isMobile) {
//   $filesToCheck[] = __DIR__.'/'.PATH_STYLE_TABLET;
//   $filesToCheck[] = __DIR__.'/'.PATH_STYLE_DESKTOP;
// }

$modifiedTimes = array_map('filemtime', $filesToCheck);
$lastModified = max($modifiedTimes);
$etag = '"' . md5(json_encode($modifiedTimes)) . '"';

// Set caching headers for 1 month, revalidate if modified
header('Cache-Control: public, max-age=2592000, must-revalidate');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 2592000) . ' GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s', $lastModified).' GMT');
header('ETag: '.$etag);
header('Vary: Accept-Encoding');

// Check for 304 Not Modified
if (
  (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $lastModified) ||
  (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag)
) {
  http_response_code(304);
  exit;
}

// Define constants
define('KEY_UNSPLASH_CLIENT_ID', "dyzWO5hxYsulu9mUAfXykunuIJXg4871mUbxRQRFQQg");
define('URL_LINKEDIN', "https://www.linkedin.com/in/taufik-nur-rahmanda/");
define('URL_INSTAGRAM', "https://www.instagram.com/txufiknr");
define('URL_TWITTER', "https://x.com/txufiknr");
define('URL_GITHUB', "https://github.com/txufiknr");
define('URL_PLAYSTORE', "https://play.google.com/store/apps/dev?id=7554821831783338570");
define('URL_WEBSITE', 'https://txufiknr.github.io');
define('URL_BASE', URL_WEBSITE.'/');
define('URL_FONT', 'https://fonts.googleapis.com/css2?family=Titillium+Web:wght@400;600&display=block');
define('LANGUAGE_OPTIONS', ['en', 'id']);
define('LANGUAGE_DEFAULT', 'en');
define('PATH_PHOTO', "assets/images/me/taufik-nur-rahmanda.webp");
define('PATH_PHOTO_SMALL', "assets/images/me/taufik-nur-rahmanda-550.webp");
define('PATH_PHOTO_TINY', "assets/images/me/taufik-nur-rahmanda-250.webp");
define('BIO_FULL_NAME', "Taufik Nur Rahmanda");
define('BIO_LEGAL_NAME', "Taufik Nur Rahmanda, S.Kom");
define('BIO_FIRST_NAME', "Taufik");
define('JOB_FIRST_YEAR', 2017);
define('JOB_LANGUAGES', 10);
define('JOB_PROJECTS', 50);

$hostURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
$hostURL.= "://".$_SERVER['HTTP_HOST'];
$pathURL = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
$baseURL = $hostURL.$pathURL;

include('functions.php');

$req_uri = strtok($_SERVER['REQUEST_URI'], '?');
$lang = $_COOKIE['language'] ?? LANGUAGE_DEFAULT;
if (!in_array($lang, LANGUAGE_OPTIONS)) $lang = LANGUAGE_DEFAULT;

$path_withlang = str_replace_first($pathURL, '', $req_uri);
$path_splitted = explode('/', $path_withlang);
if (in_array($path_splitted[0], LANGUAGE_OPTIONS)) {
  $langURL = $path_splitted[0];
  $page = count($path_splitted) > 1 ? $path_splitted[1] : '';
  
  // language code in url different with language code in the cookie
  if ($lang != $langURL) $lang = $langURL;

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

// define variables for current page
$tr = json_decode(file_get_contents("assets/lang/$lang.json"), true);
$isHome = $page == '';
$page = $isHome ? 'home' : $page;
$href = $isHome ? './' : $page;
$title = $tr['meta_title_'.$page] ?? $tr['meta_title'];
$description = $tr['meta_description_'.$page] ?? $tr['meta_description'];
$keyword = $tr['meta_keyword_'.$page] ?? $tr['meta_keyword'];
$pageTitle = BIO_LEGAL_NAME.' | '.$title;
$pageURL = $isHome ? '' : '/'.$page;
$canonical = URL_WEBSITE.($langURL ? '/'.$langURL : '').$pageURL;
$years = date('Y') - JOB_FIRST_YEAR;
// $isMobile = $isMobile || is_mobile();

// set scripts and styles paths with cache busting
$pathScript = PATH_SCRIPT.'?'.filemtime(PATH_SCRIPT);
$pathStyle = PATH_STYLE.'?'.filemtime(PATH_STYLE);
// $pathStyleTablet = PATH_STYLE_TABLET.'?'.filemtime(PATH_STYLE_TABLET);
// $pathStyleDesktop = PATH_STYLE_DESKTOP.'?'.filemtime(PATH_STYLE_DESKTOP);

// fetch hero image in home page
if ($isHome) {
  try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.unsplash.com/photos/random?query=tech&client_id='.KEY_UNSPLASH_CLIENT_ID);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $result = json_decode($response, true);
    curl_close($ch);

    $hero = $result['urls']['regular']; // full
  } catch(Exception $e) {
    $hero = 'assets/images/hero.webp'; // fallback image
  }
}
?>

<!doctype html>
<html lang="<?=$lang?>" class="no-js">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">

  <script>document.documentElement.classList.remove('no-js');</script>
  <title><?=$pageTitle?></title>

  <!-- Pre-connects -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://images.unsplash.com" crossorigin>

  <!-- Pre-load font -->
  <link rel="preload" href="<?=URL_FONT?>" as="style" fetchpriority="high" onload="this.onload=null;this.rel='stylesheet'">

  <!-- Pre-load assets -->
  <link rel="preload" href="<?=$pathStyle?>" as="style" fetchpriority="high">
  <link rel="preload" href="<?=$pathScript?>" as="script">
  <link rel="preload" href="<?=PATH_PHOTO?>"
    imagesrcset="
      <?=PATH_PHOTO_TINY?> 250w,
      <?=PATH_PHOTO_SMALL?> 550w,
      <?=PATH_PHOTO?> 720w"
    imagesizes="(max-width: 600px) 100vw, 600px"
    as="image"
    type="image/webp"
    fetchpriority="high">

  <!-- Site Info -->
  <meta name="description" content="<?=$description?>">
  <meta name="keywords" content="<?=$keyword?>">
  <meta name="author" content="<?=BIO_LEGAL_NAME?>">
  <meta name="copyright" content="<?=BIO_LEGAL_NAME?>">
  
  <!-- Open Graph -->
  <meta property="og:locale<?=$lang=='en'?'':':alternate'?>" content="en_US">
  <meta property="og:locale<?=$lang=='id'?'':':alternate'?>" content="id_ID" />
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?=$pageTitle?>">
  <meta property="og:description" content="<?=$description?>">
  <meta property="og:url" content="<?=$canonical?>">
  <meta property="og:image" content="<?=URL_BASE.PATH_PHOTO?>">
  <meta property="og:image:alt" content="<?=BIO_FIRST_NAME?>'s photo">
  <meta property="og:image:width" content="720">
  <meta property="og:image:height" content="1080">
  <meta property="og:site_name" content="<?=BIO_LEGAL_NAME?>">

  <!-- Twitter Card -->
  <meta name="twitter:site" content="@txufiknr">
  <meta name="twitter:creator" content="@txufiknr">
  <meta name="twitter:card" content="summary">

  <!-- SEO -->
  <meta name="robots" content="index, follow">
  <meta name="googlebot" content="index, follow">
  <meta name="theme-color" content="#673AB7">
  <meta name="color-scheme" content="dark light">

  <!-- Performance -->
  <meta name="format-detection" content="telephone=no">

  <!-- Structured Data (JSON-LD) -->
  <script type="application/ld+json">
  <?php include 'schema.min.php'; ?>
  </script>

  <!-- Mobile App -->
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="<?=BIO_FULL_NAME?>">
  <meta name="application-name" content="<?=BIO_FULL_NAME?>">
  
  <!-- Canonical -->
  <link rel="alternate" href="<?=URL_WEBSITE.$pageURL?>" hreflang="en">
  <link rel="alternate" href="<?=URL_BASE.'id'.$pageURL?>" hreflang="id">
  <link rel="canonical" href="<?=$canonical?>">

  <!-- Favicons -->
  <link href="favicon.png" rel="icon">
  <link href="apple-touch-icon.png" rel="apple-touch-icon">
  
  <!-- Google Font -->
  <noscript><link rel="stylesheet" href="<?=URL_FONT?>"></noscript>

  <!-- Inline critical CSS -->
  <style>
    <?php include 'assets/css/critical.min.css'; ?>
    <?php if (isset($hero)) echo '#hero { background-image: url('.$hero.'); }'; ?>
    <?php include 'assets/css/critical.i18n.min.css'; ?>
  </style>

  <!-- Main CSS -->
  <link rel="stylesheet" href="<?=$pathStyle?>">

  <!-- Defer full CSS -->
  <link rel="preload" href="assets/css/defer.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript><link rel="stylesheet" href="assets/css/defer.min.css"></noscript>
</head>

<?php ob_flush();?>

<body data-page="<?=$page?>" data-href="<?=$href?>">
  <div id="scroll-watcher"></div>
  <div id="scroll-to-top" role="button" aria-label="Scroll to top"><img alt="Scroll to top" src="assets/images/me/taufik-nur-rahmanda-64.webp"></div>

  <a class="skip-link" href="#skills">Skip to content</a>

  <header id="hero">
    <img alt="<?=BIO_FULL_NAME?>" src="<?=PATH_PHOTO?>"
      srcset="<?=PATH_PHOTO_TINY?> 250w, <?=PATH_PHOTO_SMALL?> 550w, <?=PATH_PHOTO?> 720w"
      sizes="(max-width: 600px) 100vw, 600px"
      fetchpriority="high" decoding="async" loading="eager">

    <div id="particles-js"></div>

    <div class="container">
      <!-- <h1><span>Taufik</span> Nur Rahmanda, S.Kom</h1> -->
      <h1>
        <span style="animation-delay: .2s;">Taufik</span>
        <span style="animation-delay: .4s;">Nur</span>
        <span style="animation-delay: .6s;">Rahmanda,</span>
        <span style="animation-delay: .8s;">S.Kom</span>
      </h1>
      <p class="typewrite" data-period="4000" data-type='["<?=$tr['intro_1']?>", "<?=$tr['intro_2']?>", "<?=$tr['intro_3']?>"]'>
        <span class="wrap"><?=$tr['intro_1']?></span>
      </p>
      <p><?="$tr[bio_1] $years $tr[bio_2]"?><span class="d-tablet-inline"> <?=$tr['bio_3']?></span></p>
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
  </header>

  <section id="skills">
    <div class="container">

      <div class="vendors">
        <div>
          <img width="64" height="64" loading="lazy" alt="react" src="assets/icons/vendors/react-svgrepo-com.svg">
          <img width="64" height="64" loading="lazy" alt="vue" src="assets/icons/vendors/vue-svgrepo-com.svg">
          <img width="64" height="64" loading="lazy" alt="angular" src="assets/icons/vendors/angular-icon-svgrepo-com.svg">
          <img width="64" height="64" loading="lazy" alt="nextjs" src="assets/icons/vendors/nextjs-icon-svgrepo-com.svg" style="filter: invert(.6);">
          <img width="64" height="64" loading="lazy" alt="android" src="assets/icons/vendors/android-icon-svgrepo-com.svg" style="filter: grayscale(1) brightness(1);">
          <img width="64" height="64" loading="lazy" alt="apple" src="assets/icons/vendors/apple-svgrepo-com.svg" style="filter: invert(.8);">
          <img width="64" height="64" loading="lazy" alt="firebase" src="assets/icons/vendors/firebase-svgrepo-com.svg" style="filter: grayscale(1) brightness(1);">
          <img width="64" height="64" loading="lazy" alt="kotlin" src="assets/icons/vendors/kotlin-svgrepo-com.svg">
          <img width="64" height="64" loading="lazy" alt="flutter" src="assets/icons/vendors/flutter-svgrepo-com.svg">
          <img width="64" height="64" loading="lazy" alt="dart" src="assets/icons/vendors/dart-svgrepo-com.svg">
          <img width="64" height="64" loading="lazy" alt="php" src="assets/icons/vendors/php-alt-svgrepo-com.svg" style="filter: invert();">
          <img width="64" height="64" loading="lazy" alt="codeigniter" src="assets/icons/vendors/codeigniter-svgrepo-com.svg" style="filter: grayscale(1) brightness(2);">
          <img width="64" height="64" loading="lazy" alt="mysql" src="assets/icons/vendors/mysql-svgrepo-com.svg" style="filter: grayscale(1) brightness(3);">
          <img width="64" height="64" loading="lazy" alt="bootstrap" src="assets/icons/vendors/bootstrap-svgrepo-com.svg" style="filter: grayscale(1) brightness(1.5);">
          <img width="64" height="64" loading="lazy" alt="css" src="assets/icons/vendors/css-3-official-svgrepo-com.svg" style="filter: grayscale(1) brightness(2);">
          <img width="64" height="64" loading="lazy" alt="es6" src="assets/icons/vendors/es6-svgrepo-com.svg">
          <img width="64" height="64" loading="lazy" alt="typescript" src="assets/icons/vendors/typescript-icon-svgrepo-com.svg">
          <img width="64" height="64" loading="lazy" alt="nodejs" src="assets/icons/vendors/nodejs-icon-svgrepo-com.svg">
          <img width="64" height="64" loading="lazy" alt="python" src="assets/icons/vendors/python-svgrepo-com.svg">
          <img width="64" height="64" loading="lazy" alt="unity" src="assets/icons/vendors/unity-svgrepo-com.svg" style="filter: invert(.7);">

          <img width="64" height="64" loading="lazy" alt="react" src="assets/icons/vendors/react-svgrepo-com.svg">
          <img width="64" height="64" loading="lazy" alt="vue" src="assets/icons/vendors/vue-svgrepo-com.svg">
          <img width="64" height="64" loading="lazy" alt="angular" src="assets/icons/vendors/angular-icon-svgrepo-com.svg">
          <img width="64" height="64" loading="lazy" alt="nextjs" src="assets/icons/vendors/nextjs-icon-svgrepo-com.svg" style="filter: invert(.6);">
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
        Flutter (Dart)<br>
        PHP, CodeIgniter<br>
        HTML/CSS/JS/TS<br>
        Bootstrap<br>
        jQuery<br>
        MySQL/MariaDB<br>
        PostgreSQL<br>
        Vue<br>
        React.js<br>
        Next.js<br>
        Angular<br>
        Kotlin<br>
        Java<br>
        XCode<br>
        Android Studio<br>
        Git<br>
        Firebase<br>
        Node.js<br>
        Photoshop<br>
        Vegas Pro<br>
        <!-- Cocos Creator<br> -->
        <!-- Unity<br> -->
        Express.js<br>
        Sequelize<br>
        Redis<br>
        WordPress<br>
        SEO, AMP<br>
        WebGL<br>
        UI/UX<br>
      </p>
    </div>
  </section>

  <section id="portfolio">
    <h1 class="portfolio-title">Portfolio</h1>
    <div id="particles-js2"></div>
    <div id="singularity">
      <div id="blackhole"></div>
      <div id="radiation"><i></i><i></i><i></i><i></i></div>
    </div>
    <div class="title">
      <h1><?=$tr['What_i_have']?><br><span class="typewrite" data-period="4000" data-type='["<?=$tr['created']?>", "<?=$tr['built']?>", "<?=$tr['published']?>"]'><span class="wrap"></span></span></h1>
      <h2><?=$tr['follow_me']?></h2>
    </div>
    <div class="item" id="portfolio-1">
      <img loading="lazy" alt="Qibla Locator app screenshot" src="assets/images/portfolio/qibla.webp"
        srcset="assets/images/portfolio/thumbs/qibla-150.webp 150w, assets/images/portfolio/qibla.webp 600w"
        sizes="(max-width: 600px) 35vw, 600px">
      <h2>Qibla Locator</h2>
      <div>
        <p><?=$tr['portfolio_qibla']?></p>
        <a href="https://play.google.com/store/apps/details?id=com.tarra.qibla" target="_blank" title="Download on Play Store">
          <img loading="lazy" alt="android" src="assets/icons/vendors/android-icon-svgrepo-com.svg">
        </a>
      </div>
    </div>
    <div class="item text-right" id="portfolio-2">
      <img loading="lazy" alt="Qur'an Recite app screenshot" src="assets/images/portfolio/quran.webp"
        srcset="assets/images/portfolio/thumbs/quran-150.webp 150w, assets/images/portfolio/quran.webp 600w"
        sizes="(max-width: 600px) 25vw, 600px">
      <h2>Quran Recite</h2>
      <div>
        <p><?=$tr['portfolio_quran']?></p>
        <a href="https://play.google.com/store/apps/details?id=com.tarra.alquran" target="_blank" title="Download on Play Store">
          <img loading="lazy" alt="android" src="assets/icons/vendors/android-icon-svgrepo-com.svg">
        </a>
      </div>
    </div>
    <div class="item" id="portfolio-3">
      <img loading="lazy" alt="Dhikr Counter app screenshot" src="assets/images/portfolio/tasbih.webp"
        srcset="assets/images/portfolio/thumbs/tasbih-150.webp 150w, assets/images/portfolio/tasbih.webp 600w"
        sizes="(max-width: 600px) 25vw, 600px">
      <h2>Dhikr Counter</h2>
      <div>
        <p><?=$tr['portfolio_tasbih']?></p>
        <a href="https://play.google.com/store/apps/details?id=com.tarra.tasbih" target="_blank" title="Download on Play Store">
          <img loading="lazy" alt="android" src="assets/icons/vendors/android-icon-svgrepo-com.svg">
        </a>
      </div>
    </div>
    <div class="item text-right" id="portfolio-4">
      <img loading="lazy" alt="Zakat Calculator app screenshot" src="assets/images/portfolio/zakat.webp"
        srcset="assets/images/portfolio/thumbs/zakat-150.webp 150w, assets/images/portfolio/zakat.webp 600w"
        sizes="(max-width: 600px) 25vw, 600px">
      <h2>Zakat Calculator</h2>
      <div>
        <p><?=$tr['portfolio_zakat']?></p>
        <a href="https://play.google.com/store/apps/details?id=com.tarra.zakat" target="_blank" title="Download on Play Store">
          <img loading="lazy" alt="android" src="assets/icons/vendors/android-icon-svgrepo-com.svg">
        </a>
      </div>
    </div>
    <?php include('components/socials.php'); ?>
  </section>

  <footer>&copy;<?=date('Y')?> <?=BIO_LEGAL_NAME?></footer>

  <?php if (empty($_COOKIE['language'])) { ?>
  <div id="cookie-alert">
    <p><?=$tr['cookie']?></p>
  </div>
  <?php } ?>

  <script>
    // Fetch extra JSON-LD dynamically
    fetch('assets/json/schema-extras.min.jsonld')
      .then(res => res.text())
      .then(data => {
        const el = document.createElement('script');
        el.type = 'application/ld+json';
        el.text = data;
        document.head.appendChild(el);
      });

    // Lazy-load non-essential JS
    let scriptsLoaded = false;

    async function loadScript(src) {
      return new Promise((resolve, reject) => {
        const s = document.createElement('script');
        s.src = src;
        s.async = true;
        s.onload = resolve;
        s.onerror = () => reject(new Error('Failed to load ' + src));
        document.body.appendChild(s);
      });
    }

    async function lazyLoadScripts() {
      if (scriptsLoaded) return;
      scriptsLoaded = true;

      // Remove listeners to avoid re-calling
      window.removeEventListener('scroll', lazyLoadScripts);
      window.removeEventListener('mousemove', lazyLoadScripts);
      window.removeEventListener('keydown', lazyLoadScripts);

      try {
        // Load third-party libraries in parallel
        await Promise.allSettled([
          loadScript('assets/vendor/counterup2/counterup2.js'),
          loadScript('assets/vendor/particles/particles.min.js'),
          loadScript('assets/vendor/typewrite/typewrite.min.js')
        ]);

        // Load the main script after dependencies are ready
        await loadScript('<?=$pathScript?>');
      } catch (err) {
        console.error('Failed to load scripts:', err);
      }
    }

    // Lazy-init: delay scripts until user starts interacting with the page
    window.addEventListener('scroll', lazyLoadScripts, { once: true });
    window.addEventListener('mousemove', lazyLoadScripts, { once: true });
    window.addEventListener('keydown', lazyLoadScripts, { once: true });

    // Only load animation libraries after the page is interactive
    if ('requestIdleCallback' in window) {
      requestIdleCallback(lazyLoadScripts);
    } else {
      lazyLoadScripts();
    }
  </script>
</body>
</html>