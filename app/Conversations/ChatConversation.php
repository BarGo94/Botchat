<?php

namespace App\Conversations;

use App\Http\Controllers\BotManController;
use Illuminate\Foundation\Inspiring;
use Mpociot\BotMan\Answer;
use Mpociot\BotMan\Button;
use Mpociot\BotMan\Conversation;
use Mpociot\BotMan\Question;
use Mpociot\BotMan\Facebook\ElementButton;
use Mpociot\BotMan\Facebook\Element;
use Mpociot\BotMan\Facebook\GenericTemplate;
use Mpociot\BotMan\Facebook\ButtonTemplate;
class ChatConversation extends Conversation
{
    /**
     * First question
     */
    protected $budget;

    protected $prod;
    public $prestashop;
    public function askBudget()
    {
        $question = Question::create('Quel est votre budget?')
            ->addButtons([
                Button::create('0-50D')->value('50'),
                Button::create('50-100D')->value('100'),
                Button::create('100-200D')->value('200'),
                Button::create('200-300D')->value('300'),
            ]);

        $this->ask($question, function (Answer $answer) {
            // Detect if button was clicked:
            if ($answer->isInteractiveMessageReply()) {
                $this->budget = $answer->getValue(); // will be either 'yes' or 'no'
                $this->askProd();
            }
        });
    }
    public function askProd()
    {
        $this->ask('Vous recherchez quel marque ou quel produit?(ex:Adidas,Robe,Montre....)', function(Answer $answer) {
            // Save result
            $marque=array('','robe','adidas','montre','polo','shorts','veste','gilet','survetement','survêtement','doudoune','manteau','chemise','t-shirt');
            $this->prod = $answer->getText();
            if(array_search(strtolower($this->prod),$marque)){
                $this->askifsur();
            }
            else{
                $this->say('J\'ai pas bien compris , veuillez bien préciser');
                $this->askProd();
            }
        });

    }
    public function askifsur(){
        $question = Question::create('Donc si j\'ai bien compris ,Vous cherchez comme produit : '.$this->prod.' et vous avez comme plafond : '.$this->budget.'D')
            ->addButtons([
                Button::create('Oui')->value('oui'),
                Button::create('Non')->value('non'),
            ]);

        $this->ask($question, function (Answer $answer) {
            // Detect if button was clicked:
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();
                if($selectedValue==("non")){
                    $this->askBudget();
                }
                else{
                    if(!empty((new BotManController())->searchproduct($this->prod,$this->budget,$this->prestashop))){
                    $this->bot->reply(GenericTemplate::create()
                        ->addElements((new BotManController())->searchproduct($this->prod,$this->budget,$this->prestashop)));
                    $this->bot->reply(ButtonTemplate::create('Vous voulez rechercher un nouveau produit?')
                        ->addButton(ElementButton::create('🔎 Recherche avancées')->type('postback')->payload('Bot_Aide'))
                        ->addButton(ElementButton::create('📚 Categories')->type('postback')->payload('Categories'))
                        ->addButton(ElementButton::create('🗣 Service client')->type('postback')->payload('Service Client'))
                    );}
                    else{
                        $this->bot->reply(ButtonTemplate::create('Aucun produit trouvé.😢😢 ')
                            ->addButton(ElementButton::create('🔎 Recherche avancées')->type('postback')->payload('Bot_Aide'))
                            ->addButton(ElementButton::create('📚 Categories')->type('postback')->payload('Categories'))
                            ->addButton(ElementButton::create('🗣 Service client')->type('postback')->payload('Service Client'))
                        );
                    }
                }
            }
        });    }
    /**
     * Start the conversation
     */
    public function run()
    {
        // This will be called immediately
        $this->askBudget();
    }

}
