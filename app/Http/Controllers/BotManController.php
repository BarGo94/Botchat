<?php

namespace App\Http\Controllers;

use App\Conversations\ChatConversation;
use Mpociot\BotMan\BotMan;
use Mpociot\BotMan\Facebook\ElementButton;
use Mpociot\BotMan\Facebook\GenericTemplate;
use Mpociot\BotMan\Facebook\ButtonTemplate;
use Mpociot\BotMan\Question;
use Mpociot\BotMan\Button;
use Mpociot\BotMan\Facebook\Element;
use Mpociot\BotMan\Answer;
use Protechstudio\PrestashopWebService\PrestashopWebService;


class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    protected $botman;
    public function handle(PrestashopWebService $prestashop)
    {
        $botman = app('botman');
        $botman->verifyServices(env('TOKEN_VERIFY'));
        $this->prestashop = $prestashop;
        /*Button Démarrer*/
        $botman->hears('GET_STARTED_PAYLOAD', function (BotMan $bot) {
            $user = $bot->getUser();
            $bot->reply('Salut ' . $user->getFirstName() . ' 🙋 🎉');
            $bot->reply('Je suis l\'assistant artificiel de Access 💄 👢 👔.');

            $bot->reply(ButtonTemplate::create('Comment je peux vous aider ?')
                ->addButton(ElementButton::create('🔎 Recherche avancées')->type('postback')->payload('Bot_Aide'))
                ->addButton(ElementButton::create('🗣 Service Client')->type('postback')->payload('Service Client'))
            );
            $bot->reply(Question::create('Découvrez aussi nos offres.')
                ->addButtons([
                    Button::create('Categories')->value('Categories'),
                    Button::create('Nouveautes')->value('Nouveautes'),
                ]));
        });
        $botman->hears('Nouveautes', function (BotMan $bot) {
            $bot->reply(GenericTemplate::create()
                ->addElements($this->createProductCarousel((new PrestaController())->ProductNouv($this->prestashop)))
            );
            $bot->reply(Question::create('Vous voulez autre chose? ')
                ->addButtons([
                    Button::create('Categories')->value('Categories'),
                    Button::create('Recherche avancees')->value('Recherche avancees'),
                    Button::create('Service Client')->value('Service Client'),
                ]));
        });

        $botman->hears('Service Client', function (BotMan $bot) {
            $bot->reply(ButtonTemplate::create('Comment vous voulez contacter le service client?')
                ->addButton(ElementButton::create('ℹ Contactez-Nous')->url('https://aber.tn/nous-contacter'))
                ->addButton(ElementButton::create('📱 Appelez-nous')
                    ->type('phone_number')->payload('+0021655778899'))
            );

        });
        /*Fin Button Démarrer*/
        /*Options*/
        $botman->hears('Bot_Aide', function (BotMan $bot) {
            $c=new ChatConversation();
            $c->prestashop=$this->prestashop;
            $bot->startConversation($c);
        });
            $botman->hears('Categories', function (BotMan $bot) {
            $bot->reply(GenericTemplate::create()
                ->addElements($this->createCategoriesCarousel((new PrestaController())->Category($this->prestashop)))
            );
            $bot->reply(Question::create('Si vous voulez, vous pouvez ecrire un nom de marque ou de produit pour rechercher 😎  ')
                ->addButtons([
                    Button::create('Nouveautes')->value('Nouveautes'),
                    Button::create('Services Client')->value('Services Client'),
                ]));
        });
        $botman->hears('Recherche avancees', function (BotMan $bot) {
            $c=new ChatConversation();
            $c->prestashop=$this->prestashop;
            $bot->startConversation($c);
        });
        /*Trouver cat ou produit */
        $botman->hears('ProdouCat_{name}', function (BotMan $bot, $name) {
            $var1 = (new PrestaController())->SousCat((new PrestaController())->getCatId($name, $this->prestashop), $this->prestashop);
            if ($var1 != null) {
                $bot->reply(GenericTemplate::create()
                    ->addElements($this->createCategoriesCarousel((new PrestaController())->SousCat((new PrestaController())->getCatId($name, $this->prestashop), $this->prestashop)))
                );
            } else {
                if(!empty($this->createProductCarousel((new PrestaController())->MyProductsByIdCat((new PrestaController())->getCatId($name, $this->prestashop), $this->prestashop)))){
                    $bot->reply(GenericTemplate::create()
                        ->addElements($this->createProductCarousel((new PrestaController())->MyProductsByIdCat((new PrestaController())->getCatId($name, $this->prestashop), $this->prestashop)))
                    );
                    $bot->reply(Question::create('Produits trouvées  !! 🙌🙌')
                        ->addButtons([
                            Button::create('Recherche avancees')->value('Bot_Aide'),
                            Button::create('Services Client')->value('Services Client'),
                        ]));
                }
else{
                $bot->reply(Question::create('Aucun produit pour le moment  !! 😢😢')
                    ->addButtons([
                        Button::create('Recherche avancées')->value('Bot_Aide'),
                        Button::create('Services Client')->value('Services Client'),
                    ]));}
            }

        });
        $botman->hears('Nouvelle recherche', function (BotMan $bot) {
            $bot->reply(Question::create('Pour effectuer une nouvelle recherche: Saisissez: nom de marque ou nom de produit.
Exemple de Marque:Adidas , Nike ... 
Exemple de nom produit: Baskets... ')
                ->addButtons([
                    Button::create('Categories')->value('Categories'),
                    Button::create('Services Client')->value('Services Client'),
                ]));
        });

        $botman->fallback(function (Botman $bot) {
            $text = $bot->getMessage()->getMessage();
            $bonjour = array("","bonjour", "bonsoir", "salut", "hello", "hi", "ahla");
            if (array_search(strtolower($text), $bonjour)) {
                $user = $bot->getUser();
                $bot->reply('Salut ' . $user->getFirstName() . ' 🙋 🎉');
                $bot->reply('Je suis l\'assistant artificiel de Anas 💄 👢 👔.');

                $bot->reply(ButtonTemplate::create('Comment je peux vous aider ?')
                    ->addButton(ElementButton::create('🔎 Recherche avancées')->type('postback')->payload('Bot_Aide'))
                    ->addButton(ElementButton::create('🗣 Service client')->type('postback')->payload('Service Client'))
                );
                $bot->reply(Question::create('Découvrez aussi nos offres.')
                    ->addButtons([
                        Button::create('Categories')->value('Categories'),
                        Button::create('Nouveautes')->value('Nouveautes'),
                    ]));
            } else {
                if ((new PrestaController())->MyProductByName($text, $this->prestashop)) {
                    $bot->reply(GenericTemplate::create()
                        ->addElements($this->createProductCarousel((new PrestaController())->MyProductByName($text, $this->prestashop)))
                    );
                    $bot->reply(Question::create('Produits trouvées  !! 🙌🙌')
                        ->addButtons([
                            Button::create('Recherche avancées')->value('Bot_Aide'),
                            Button::create('Categories')->value('Categories'),
                            Button::create('Service Client')->value('Service Client'),
                        ]));
                } else {
                    $bot->reply(Question::create('Je n’arrive pas à trouver ce que vous cherchez.
Essayez de nouveau avec des termes plus généraux ou contacter notre service client.')
                        ->addButtons([
                            Button::create('Recherche avancées')->value('Bot_Aide'),
                            Button::create('Categories')->value('Categories'),
                            Button::create('Services Client')->value('Services Client'),
                        ]));
                }
            }
        });
        /*Fin produit selon marque ou nom*/
        $botman->listen();
    }

    public function createCategoriesCarousel($items)
    {
        $array[] = array();
        foreach ($items as $item) {
            $array[] = Element::create($item['nom_category'])
                ->image('http://aber.tn/img/tmp/category_' . $item['id_category'] . '.jpg')
                ->addButton(ElementButton::create('🚪 Consulter')
                    ->payload('ProdouCat_' . $item['nom_category'])->type('postback'));
        }
        return $array;
    }

    public function createProductCarousel($items)
    {

        $array[] = array();
        foreach ($items as $item) {
            $array[] = Element::create($item['nom_prod'])
                ->subtitle('Prix:' . $item['prix'])
                ->image('http://aber.tn/' . $item['id_image'] . '-large_default/' . $item['id_image'] . '.jpg')
                ->addButton(ElementButton::create('🛒 Acheter')->url('http://aber.tn/' . $item['id_product'] . '-' . str_replace(' ', '-', $item['nom_prod']) . '.html'));
        }

        return $array;
    }
    public function searchproduct($nomproduct,$budget,$prestashop){
        return $this->createProductCarousel((new PrestaController())->MyProductByNameAndPrice($nomproduct,$budget,$prestashop));
    }
    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
}
