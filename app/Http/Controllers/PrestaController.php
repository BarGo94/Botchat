<?php

namespace App\Http\Controllers;
use Protechstudio\PrestashopWebService\PrestashopWebService;

use Illuminate\Http\Request;

class PrestaController extends Controller
{

    public function bar()
    {
        $this->Category();

    }
    public function Category(PrestashopWebService $prestashop)
    {
        $this->prestashop = $prestashop;
        $arr= array();
        $opt = array(
            'resource' => 'categories',
            'filter[id_parent]'=>'[2]',
            'display' => 'full',
        );
        $xml = $this->prestashop->get($opt);
        $resources = $xml->categories->children()->category;
        foreach ($resources as $cat) {
            $arr[] = array('id_category' => (int)$cat->id, 'nom_category' => $cat->name->language[0]->__toString());
        }
        return $arr;

    }
    public function SousCat($idcat,PrestashopWebService $prestashop){
        $this->prestashop = $prestashop;
        $arr= array();
        $opt = array(
            'resource' => 'categories',
            'filter[id_parent]'=> $idcat,
            'display' => 'full',
        );
        $xml = $this->prestashop->get($opt);

        $resources = $xml->categories->children();

        foreach ($resources as $souscat) {
            $arr[] = array('id_category' => (int)$souscat->id, 'nom_category' => $souscat->name->language[0]->__toString());
        }
        return $arr;
    }


    public function MyProductsByIdCat($idcat,PrestashopWebService $prestashop)
    {
        $this->prestashop = $prestashop;
        $arr= array();
        $opt = array(
            'resource' => 'products',
            'filter[id_category_default]' => $idcat,
            'display' => 'full',
        );
        $xml = $this->prestashop->get($opt);
        $resources = $xml->products->children()->product;
        foreach ($resources as $prod) {
            $arr[] = array('id_product' => (int)$prod->id, 'nom_prod' => $prod->name->language[0]->__toString(), 'id_image' => (int)$prod->id_default_image,'prix' =>number_format((float)$prod->price, 2, ',', ''));
        }
        return $arr;

    }

    public function ProductNouv(PrestashopWebService $prestashop)
    {
        $this->prestashop = $prestashop;
        $arr= array();
        $opt = array(
            'resource' => 'products',
            'sort' => '[id_DESC]',
            'display' => 'full',
            'limit'=> '4',
        );
        $xml = $this->prestashop->get($opt);
        $resources = $xml->products->children()->product;
        foreach ($resources as $prod) {
            $arr[] = array('id_product' => (int)$prod->id, 'nom_prod' => $prod->name->language[0]->__toString(), 'id_image' => (int)$prod->id_default_image,'prix' =>number_format((float)$prod->price, 2, ',', ''));
        }
        return $arr;

    }

    public function MyProductByName($name,PrestashopWebService $prestashop)
    {
        $this->prestashop = $prestashop;
        $arr= array();
        $opt = array(
            'resource' => 'products',
            'filter[name]' =>'%['.$name.']%',
            'limit'=>'4',
            'display' => 'full',
        );
        $xml = $this->prestashop->get($opt);
        $resources = $xml->products->children();
        foreach ($resources as $prod) {
            $arr[] = array('id_product' => (int)$prod->id, 'nom_prod' => $prod->name->language[0]->__toString(), 'id_image' => $prod->id_default_image,'prix' =>number_format((float)$prod->price, 2, ',', ''));
        }
        return $arr;

    }
    public function MyProductByNameAndPrice($name,$price,PrestashopWebService $prestashop)
    {
        $this->prestashop = $prestashop;
        $arr= array();
        $opt = array(
            'resource' => 'products',
            'filter[name]' =>'%['.$name.']%',
            'filter[price]'=>'[0,'.$price.']',
            'limit'=>'4',
            'display' => 'full',
        );
        $xml = $this->prestashop->get($opt);
        $resources = $xml->products->children();
        foreach ($resources as $prod) {
            $arr[] = array('id_product' => (int)$prod->id, 'nom_prod' => $prod->name->language[0]->__toString(), 'id_image' => $prod->id_default_image,'prix' =>number_format((float)$prod->price, 2, ',', ''));
        }
        return $arr;

    }
    public function getCatId($name,PrestashopWebService $prestashop)
    {
        $this->prestashop = $prestashop;
        $opt = array(
            'resource' => 'categories',
            'filter[name]' =>'%['.$name.']%',
            'display' => 'full',
        );
        $xml = $this->prestashop->get($opt);
        $resources = $xml->categories->children();
        return (int) $resources->category->id;
    }
}

