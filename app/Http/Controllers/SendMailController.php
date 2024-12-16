<?php

namespace App\Http\Controllers;

use App\Jobs\MailJobs;
use Illuminate\Support\Facades\Mail;

class SendMailController extends Controller
{
    public function send()
    {

        $data = [
            "isSuccess" => false,
            "user_pseudo" => "Didier bikie",
            "email" => "didier.bikie@gmail.com",
            "conditions"=>["premiere condition ","deuxieme condition ","troixieme condition ",]
        ];

        return $this->run("App\Mail\ReservationAnnuleParExpediteurInterphaseVoyageur", $data);
    }
    public function run($calback, $data)
    {

       /*  Mail::to($data["email"])->send(new $calback($data));
        return "okok"; */
    
    
                dispatch(new MailJobs($calback,$data));
        
    }

    public function template()
    {
        $data = [
            "isSuccess" => true,
            "user_pseudo" => "franky steve",
            "title" => "Votre certification",
            "link" => "Votre certification",

        ];
    }

    public function extract($data)
    {
        $title = "Bonjour " . $data["user_pseudo"];
        return $data["isSuccess"] ? $title . ",\n 
        Ca y est, nous avons le plaisir de vous informer que vous bénéficiez du statut vérifié sur Luggin. Votre numéro de téléphone, vos informations d’identité et votre pièce d’identité ont été validées.\n\n
        En tant que utilisateur certifié, vous pouvez désormais:\n\n
        Effectuer des transactions avec un voyageur ou un expéditeur sans être sur le même vol.\n\n" :
            $title . ",\n La vérification des informations que vous avez fournies a échoué.
        Cette démarche est importante et doit
        être effectuée pour que vous puissiez profiter de toutes les fonctionnalités de Luggin..\n
        Veuillez vérifier, soumettre une nouvelle fois vos données en vous assurant qu’elles sont authentiques, lisibles et concordantes.";
    }
}
