<?php
/*
Template Name: Statistiques Blog
*/
?>

<?php get_header(); ?>
<div class="content">
<div class="entry">
<div class="post-inner">

<h1 class="name post-title entry-title">Le blog en quelques chiffres....</h1>

<?php 

// Chargement de la bibliothèque de clients PHP de l'API Google.
require_once ABSPATH . 'vendor/autoload.php';


// Initialisation de l'API Google
$analytics = initializeAnalytics();

// Sélection du premier profil disponible sur Google Analytics
$profile = getFirstProfileId($analytics);

// Introduction de la page
echo "<br>";
echo "<center><div class='aligncenter size-large'><img src='https://mon-site.com/wp-content/uploads/Analytics_2.jpg' alt='Statistique du blog en temps réel.' class='wp-image-913700'/></div></center>";
echo "<br>";
echo "Pour les futurs annonceurs et les curieux, voici quelques statistiques en temps réel de mon blog. ";
echo "<br>";
echo "<br>";
echo "<H4><strong>Ce que je connais sur vous (à cause des indiscrétions de votre navigateur).</strong></H4>";

// récupération d'informations provenant du navigateur du visiteur
include_once (ABSPATH . 'geoip/geoipcity.inc'); 
include_once ('/home/mon-site/public_html/geoip/geoipregionvars.php'); 

$gi = geoip_open(ABSPATH . 'geoip/GeoLiteCity.dat', GEOIP_STANDARD); 
$record = geoip_record_by_addr($gi,$_SERVER['REMOTE_ADDR']); 

$user_agent = $_SERVER['HTTP_USER_AGENT']; 
$ip = $_SERVER['REMOTE_ADDR']; 
$url = $_SERVER['HTTP_REFERER']; 
$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']); 
$pays = $record->country_name;
$langs=explode(",",$_SERVER["HTTP_ACCEPT_LANGUAGE"]);
$var_resol=resolution();

if (preg_match ("/Win/", getenv("HTTP_USER_AGENT")))
$os = "Windows";
elseif ((preg_match ("/Mac/", getenv("HTTP_USER_AGENT"))) || (preg_match ("/PPC/", getenv("HTTP_USER_AGENT"))))
$os = "Mac";
elseif (preg_match ("/Linux/", getenv("HTTP_USER_AGENT")))
$os = "Linux";
elseif (preg_match ("/FreeBSD/", getenv("HTTP_USER_AGENT")))
$os = "FreeBSD";
elseif (preg_match ("/SunOS/", getenv("HTTP_USER_AGENT")))
$os = "SunOS";
elseif (preg_match ("/IRIX/", getenv("HTTP_USER_AGENT")))
$os = "IRIX";
elseif (preg_match ("/BeOS/", getenv("HTTP_USER_AGENT")))
$os = "BeOS";
elseif (preg_match ("/OS/2/", getenv("HTTP_USER_AGENT")))
$os = "OS/2";
elseif (preg_match ("/AIX/", getenv("HTTP_USER_AGENT")))
$os = "AIX";
else
$os = "Autre";

echo " Votre ip publique est : <strong>$ip</strong>.";
echo "<br>";
echo " Votre pays de provenance est : <strong>$pays</strong>.";
echo "<br>";
echo " Votre host est : <strong>$hostname</strong>.";
echo "<br>";
echo " Votre OS est : <strong>$os</strong>.\n";
echo "<br>";
echo "Votre langue principale est: <strong>$langs[0]</strong>.";
echo "<br>";
echo "Votre résolution actuelle est : <strong>$var_resol</strong>.";
echo "<br>";
echo "Votre navigateur actuel est : <strong>$user_agent</strong>.";
echo "<br>";
echo "Votre URL de provenance est : <strong>$url</strong>.";

// Affichage des statistiques du blog sur plusieurs périodes
$results = getResultstodays($analytics, $profile);
$realtime = get_realtime_active_user($analytics, $profile);
$realtimepagesview = get_realtime_pages_view($analytics, $profile);
$resultsyesterday = getResultsyesterday($analytics, $profile);
printResultsRealTime($realtime);
printResultsRealTimePagesView($realtimepagesview);

// Récupération de la date du jour et de l'heure
date_default_timezone_set('Europe/Paris');
$today = date("d-m-Y");  
$time = date("H:i:s");
echo "<H5><strong><i>" . "Stats du" . " $today" . " (aujourd'hui) à" . " $time.". "</i></strong></H5>";

// Affichage des statistiques du jour
printResults($results);
echo "<H5><strong><i>" . "Stats du " . DatedHier() . " (hier)." . "</i></strong></H5>";

// Affichage des statistiques J-1
printResults($resultsyesterday);

// Fonction permettant d'initialiser l'accès à GA
function initializeAnalytics()
{
  // Creates and returns the Analytics Reporting service object.

  // Use the developers console and download your service account
  // credentials in JSON format. Place them in this directory or
  // change the key file location if necessary.
  $KEY_FILE_LOCATION = ABSPATH . 'api-stat-blog-123456.json';

  // Create and configure a new client object.
  $client = new Google_Client();
  $client->setApplicationName("Hello Analytics Reporting");
  $client->setAuthConfig($KEY_FILE_LOCATION);
  $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
  $analytics = new Google_Service_Analytics($client);

  return $analytics;
}

// Fonction permettant de récupérer le premier profil disponible sur votre compte GA
function getFirstProfileId($analytics) {
  
  // Get the list of accounts for the authorized user.
  $accounts = $analytics->management_accounts->listManagementAccounts();

  if (count($accounts->getItems()) > 0) {
    $items = $accounts->getItems();
    $firstAccountId = $items[0]->getId();

    // Get the list of properties for the authorized user.
    $properties = $analytics->management_webproperties
        ->listManagementWebproperties($firstAccountId);

    if (count($properties->getItems()) > 0) {
      $items = $properties->getItems();
      $firstPropertyId = $items[0]->getId();

      // Get the list of views (profiles) for the authorized user.
      $profiles = $analytics->management_profiles
          ->listManagementProfiles($firstAccountId, $firstPropertyId);

      if (count($profiles->getItems()) > 0) {
        $items = $profiles->getItems();

        // Return the first view (profile) ID.
        return $items[0]->getId();

      } else {
        throw new Exception('No views (profiles) found for this user.');
      }
    } else {
      throw new Exception('No properties found for this user.');
    }
  } else {
    throw new Exception('No accounts found for this user.');
  }
}

// Fonction permettant d'afficher les statistiques du jour
function getResultstodays($analytics, $profileId) {
  
   return $analytics->data_ga->get(
       'ga:' . $profileId,
       'today',
       'today',
       'ga:users,ga:sessions,ga:pageviews,ga:pageviewsPerSession,ga:avgSessionDuration,ga:avgPageLoadTime');
}

// Fonction permettant d'afficher les statistiques de la veille
function getResultsyesterday($analytics, $profileId) {
  
   return $analytics->data_ga->get(
       'ga:' . $profileId,
       'yesterday',
       'yesterday',
       'ga:users,ga:sessions,ga:pageviews,ga:pageviewsPerSession,ga:avgSessionDuration,ga:avgPageLoadTime');
}

// Fonction permettant d'afficher le nombre de visiteurs en temps réel sur le blog depuis GA
function get_realtime_active_user($analytics, $profileId){

   return $analytics->data_realtime->get(
       'ga:' . $profileId,
       'rt:activeUsers');  
}

// Fonction permettant d'afficher le nombre de pages vues en 1 minute sur le blog depuis GA
function get_realtime_pages_view($analytics, $profileId){

   return $analytics->data_realtime->get(
       'ga:' . $profileId,
       'rt:pageviews');  
}

function printResults($results) {
  // Parses the response from the Core Reporting API and prints
  // the profile name and total sessions.
  if (count($results->getRows()) > 0) {

    // Get the profile name.
    $profileName = $results->getProfileInfo()->getProfileName();

    // Récupération des résultats depuis le tableau
    $rows = $results->getRows();
    $users = $rows[0][0];
    $sessions = $rows[0][1];
    $pageviews = $rows[0][2];
    $pageviewspersession = $rows[0][3];
    $pageviewspersession = round($pageviewspersession,2);
    $avgSessionDuration = $rows[0][4];
    $avgSessionDuration = round($avgSessionDuration);
    $avgPageLoadTime = $rows[0][5];
    $avgPageLoadTime = round($avgPageLoadTime,2);
    
     // Affichage des résultats
    echo "Profil Analytics trouvé:" . "<strong>" . " $profileName\n" . "</strong>";
    echo "<br>";
    echo "Visiteurs uniques:" . "<strong>" . " $users" . "</strong>" . " - Sessions visiteurs:" . "<strong>" ." $sessions\n". "</strong>" ." - Pages vues: " . "<strong>" ." $pageviews". "</strong>" . " - Pages vue par sessions:" . "<strong>" ." $pageviewspersession\n". "</strong>" ;
    echo "<br>";
    echo "Durée moyenne d'une session: " . "<strong>" .  " $avgSessionDuration secondes" . "</strong>" . " - Temps de chargement moyen des pages: " . "<strong>" . "$avgPageLoadTime secondes\n" . "</strong>" ;
  } else { 
    print "No results found.\n"; 
  }
}

function printResultsRealTime($results1) {
  
  if (count($results1->getRows()) > 0) {

    $rows = $results1->getRows();
    $realtimeusers = $rows[0][0];

    // Affichage des résultats
    echo "<H4><strong> Actuellement sur le blog.</strong></H4>";
    echo "Utilisateurs actifs:" . "<strong>" . " $realtimeusers\n" . "</strong>" ;
  
  } else {
   
    echo "<br>";
    echo "No results found.\n";
    echo "<br>";
  }
}

// Fonction permettant d'afficher les pages vues sur les 30 dernières minutes
function printResultsRealTimePagesView($results2) {
  
  if (count($results2->getRows()) > 0) {

    // Get the entry for the first entry in the first row.
    $rows = $results2->getRows();
    $realtimepageviews = $rows[0][0];

    // Affichage des résultats
    echo " | Pages vues sur les dernières 30 minutes:" . "<strong>" . " $realtimepageviews\n" . "</strong>" ;
    
  } else {
 
    echo "<br>";
    echo "No results found.\n";
    echo "<br>";
  }
}

// Fonction permettant d'afficher la date d'hier.
function DatedHier($date=''){
     if ($date=='') 
     { 
      $mois = date("m"); 
      $jour = date("d"); 
      $annee = date("Y"); 
     } 
     else 
     { 
      $annee = substr($date, 0, 4); 
      $mois = substr($date, 5, 2); 
      $jour = substr($date, 8, 2); 
      if (checkdate($mois, $jour, $annee)==false) 
      return -1; 
     }; 
     $hier = getdate(mktime(0, 0, 0, $mois, $jour - 1, $annee)); 
     if ($hier['mon']<10)
      $hier['mon'] = "0".$hier['mon'];
     if ($hier['mday']<10)
      $hier['mday'] = "0".$hier['mday'];
     $hier = $hier['mday']."-".$hier['mon']."-".$hier['year'];
      
     return $hier; 
    }

?>

<h4><strong>Nombre de visites sur 1 mois.</strong></h4>
<center><iframe style="width: 540px; height: 270px; border: none;" src="https://www.seethestats.com/stats/4587/Visits_a46db4d37_ifr.html" width="320" height="240" frameborder="0" scrolling="no"></iframe></center>
<h4><strong>Nombre de pages vues sur 1 mois.</strong></h4>
<center><iframe style="width: 540px; height: 270px; border: none;" src="https://www.seethestats.com/stats/4587/Pageviews_78e0ea04b_ifr.html" width="320" height="240" frameborder="0" scrolling="no"></iframe></center>
</div>
</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
