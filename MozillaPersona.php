<?php
/**
 * Mozilla Persona extension for MediaWiki
 * 
 * PHP Version 5.4.6
 * 
 * @category Extension
 * @package  MozillaPersona
 * @author   Pierre Rudloff <contact@rudloff.pro> 
 * @license  GNU General Public License http://www.gnu.org/licenses/gpl.html
 * @link     http://rudloff.pro/
 * */
$wgExtensionCredits['MozillaPersona'][] = array(
   'name' => 'MozillaPersona',
   'author' =>'Pierre Rudloff'
);

$messages = array();


$wgExtensionMessagesFiles['MozillaPersona']
    = dirname(__FILE__).'/MozillaPersona.i18n.php';

/**
 * Class for login page
 * 
 * PHP Version 5.4.6
 * 
 * @category Extension
 * @package  MozillaPersona
 * @author   Pierre Rudloff <contact@rudloff.pro> 
 * @license  GNU General Public License http://www.gnu.org/licenses/gpl.html
 * @link     http://rudloff.pro/
 * */
class SpecialMozillaPersona extends SpecialPage
{
    /**
     * SpecialMozillaPersona constructor
     * 
     * @return void
     * */
    function __construct()
    {
            parent::__construct('MozillaPersona');
    }

    /**
     * Display login page
     * 
     * @param string|null $subPage ?
     * 
     * @return void
     * */
    function execute($subPage)
    {
        $request = $this->getRequest();
        $output = $this->getOutput();
        $this->setHeaders();
        
        if (isset($_POST['assertion'])) {
            $url = "https://browserid.org/verify";
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt(
                $curl, CURLOPT_POSTFIELDS, "assertion=".strval(
                    $_POST["assertion"]
                )."&audience=".$_SERVER['HTTP_HOST']
            );
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $response=json_decode(strval(curl_exec($curl)));
            curl_close($curl);
                             
            if ($response->status==='okay') {
                $dbr = wfGetDB(DB_SLAVE);
                $res = $dbr->select(
                    'user', array('user_id'),
                    'user_email = \''.$response->email.'\''
                );
                foreach ( $res as $row ) {
                    $user=User::newFromId($row->user_id);
                    break;
                }
                if (isset($user)) {
                    $user->setCookies();
                    wfRunHooks(
                        'UserLoginComplete',
                        array( &$currentUser, &$injected_html )
                    );
                    $this->getOutput()->redirect('index.php?title=Accueil');
                } else {
                    $output->addWikiText(wfMsg('unknown_email'));
                }
            } else {
                $output->addWikiText(
                    wfMsg('error').' \'\''.$response->reason.'\'\''
                );
            }
        }
        $output->addHTML(file_get_contents(__DIR__.'/form.html'));
    }
}

$wgSpecialPages['MozillaPersona'] = 'SpecialMozillaPersona';
?>
