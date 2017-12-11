<?php

namespace App\Conversations;

use Illuminate\Foundation\Inspiring;
use Mpociot\BotMan\Answer;
use Mpociot\BotMan\Button;
use Mpociot\BotMan\Conversation;
use Mpociot\BotMan\Question;

class ChatConversation extends Conversation
{
    /**
     * First question
     */
    protected $budget;

    protected $prod;

    public function askBudget()
    {
        $question = Question::create('Quel est votre budget?')
            ->addButtons([
                Button::create('0-50D')->value('50'),
                Button::create('50-100D')->value('100'),
                Button::create('Plus')->value('')
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
            $marque=array('','robe','adidas','montre','anas');
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
                if($selectedValue=='Non'){
                    $this->askBudget();
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
