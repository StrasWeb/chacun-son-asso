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
    <title>Chacun son asso - Résultats de recherche</title>
    <?php require 'head.php'; require 'config.php'; ?>
        <h1>Chacun son asso</h1>
        </header>
        <div data-role="content">
<?php
$dep=isset($_GET['dep'])?$_GET['dep']:'';
$results=json_decode(
    file_get_contents(
        $api.'?action=search&asso='.urlencode($_GET['name']).
        '&dep='.$dep.'&them='.$_GET['theme']
    )
);
if ($results->status=='okay') {
    ?>
                <ul data-role="listview">
    <?php
    $assos=$results->list;
    foreach ($assos as $asso) {
        $asso->{'Sigle ou acronyme'}=isset($asso->{'Sigle ou acronyme'})?
            $asso->{'Sigle ou acronyme'}:$asso->{'Nom complet'};
        print(
            '<li><a data-ajax="false" href="asso.php?name='.
            urlencode($asso->{'Nom complet'}).'&id='.$asso->id.
            '"><abbr title="'.$asso->{'Nom complet'}.'">'.
            $asso->{'Sigle ou acronyme'}.'</abbr></a></li>'
        );
    }
    ?>
            </ul>
    <?php
} else {
    print('<p><b>Erreur</b>&nbsp;: '.$results->error.'</p>');
}
    ?>
        
        </div>

 </div>
</body>
</html>
