<?php
/**
 * Version mobile de l'annuaire Animafac
 * 
 * PHP version 5.4.6
 * 
 * @category Mobile
 * @package  Mobile_Animafac
 * @author   Pierre Rudloff <rudloff@strasweb.fr>
 * @license  GPL http://www.gnu.org/licenses/gpl.html
 * @link     http://strasweb.fr/
 * */
?>
<!Doctype HTML>
<html>
    <head>
        <?php
require 'config.php';
$dep=isset($_GET['dep'])?$_GET['dep']:'';
$results=json_decode(
    file_get_contents(
        $api.'?action=search&asso='.urlencode($_GET['name']).'&dep='.$dep
    )
);
if ($results->status=='okay') {
    $assos=$results->list;
    foreach ($assos as $asso) {
        if ($asso->id == $_GET['id']) {
            $myAsso=$asso;
        }
    }
}
        ?>
    <title>Chacun son asso 
    <?php
if (isset($myAsso)) {
    print(' - '.$myAsso->{'Nom complet'});
}
    ?>
    </title>
    <?php require 'head.php'; ?>
        <h1>Chacun son asso</h1>
        <?php
if (isset($_SERVER['HTTP_REFERER'])) {
    $referer=parse_url($_SERVER['HTTP_REFERER']);
    if ($referer['host'] == $_SERVER['SERVER_NAME']) {
        print(
            '<a href="'.$_SERVER['HTTP_REFERER'].
            '" data-icon="back" data-iconpos="notext">Retour</a>'
        );
    }
}
        ?>
        </header>
        <div data-role="content" itemscope
        itemtype="http://schema.org/NGO">
        <?php
if (isset($myAsso)) {
    print(
        '<h2><abbr itemprop="name" title="'.$myAsso->{'Nom complet'}.'">'.
        $myAsso->{'Sigle ou acronyme'}.'</abbr></h2>'
    );
            ?>
            <div id="mapdiv" class="map" style="width:480px; height:320px;"></div>
            <script src="http://www.openlayers.org/api/OpenLayers.js"></script>
            <script src="map.js" async="async"></script>
            <table>
            <?php
    foreach ($myAsso as $info=>$value) {
        if ($info != 'id') {
            switch ($info) {
            case 'Nom complet':
                $meta='legalName';
                break;
            case 'Sigle ou acronyme':
                $meta='brand';
                break;
            case 'Adresse':
                $meta='address';
                break;
            case 'Téléphone':
                $meta='telephone';
                break;
            case 'e-mail':
                $meta='email';
                $emails=explode("\r\n\t\t\t\r\n\t\t\t", $value);
                $value='';
                foreach ($emails as $email) {
                    $value.="<a href='mailto:$email'>$email</a><br/>";
                }
                break;
            case 'Site internet':
                $meta='url';
                $value="<a href='$value'>$value</a>";
                break;
            case 'Zoom':
                $meta='description';
                break;
            }
            print("<tr><th>$info</th><td");
            if (isset($meta)) {
                print(" itemprop='$meta'");
            }
            print(">$value</td></tr>");
        }
    }
            ?>
            </table>
            <?php
} else {
    print('<p><b>Erreur</b>&nbsp;: '.$results->error.'</p>');
}
        ?>
        
        
    <?php require 'footer.php'; ?>
    
