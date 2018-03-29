<?php
namespace Tests\JD\LouvreBundle\Services\OutilsBilletsTest;

use JD\LouvreBundle\Services\OutilsBillets\OutilsBillets;
use PHPUnit\Framework\TestCase;
use Symfony\Component\VarDumper\Cloner\Data;

class OutilsBilletsTest extends TestCase
{
    public Function testCalculAge()
    {
        $billet = new OutilsBillets();
        $date =  new \DateTime('now');
        $date = $date->modify('-44 years');
        $age = $billet->calculAge($date);
        $this->assertEquals(44, $age);
    }

    public function testCalculPrix()
    {
        $prixBillets = new OutilsBillets();
        $age = 41;
        $prixBillets->calculPrix($age);
        $this->assertEquals(16, $prixBillets);
    }
}