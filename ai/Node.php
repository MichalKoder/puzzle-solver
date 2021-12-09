<?php

class Node {
    private $state; // wierzchołek przechowuje informację o stanie gry
    private $ops; // węzeł przechowuje dane o możliwych wyzwalaczach zmiany stanu (w tym przypadku ruchach)
    private $lastop; // węzeł z wyzwalaczem, który spowodował przejście do $state
    private $parent; // oraz referencję do rodzica, żeby móc się po czym wspinać od liścia do korzenia ;)
    private $depth; // głębokość wierzchołka w drzewie
    private $cost; // koszt obliczony dla stanu tego węzła
    private $costTotal;// koszt obliczony dla całej ścieżki od korzenia do tego węzła
    private $id; // id węzła w kolejności dodawania do drzewa
    
    public function __construct($state, $ops, $lastop, $parent, $depth, $cost=0) {
        $this->state = $state;
        $this->parent = $parent;
        $this->depth = $depth;
        $this->ops = $ops;
        $this->lastop = $lastop;
        $this->cost = $cost;
        $this->costTotal = ($this->getParent() != null) ? ($this->getParent()->getCostTotal() + $cost) : 0;
        
    }

    public function unsetOp($index) {
        $i = array_search($index,$this->ops);
        unset($this->ops[$i]);
        $this->ops = array_values($this->ops); 
    }

    public function getOps() {
        return $this->ops;
    }

    public function setOps($ops) {
        $this->ops = $ops;
    }

    public function popOp() {
        return array_pop($this->ops);
    }

    public function pushOp($op) {
        $this->ops[] = $op;
    }


    // GETTERS AND SETTERS

    public function getState() {
        return $this->state;
    }

    public function setState($s) {
        $this->state = $s;
    }

    public function getParent() {
        return $this->parent;
    }

    public function setParent($p) {
        $this->parent = $p;
    }

    public function getDepth() {
        return $this->depth;
    }

    public function setDepth($d) {
        $this->depth = $d;
    }

    public function setCost($cost) {
        $this->cost = $cost;
    }

    public function getCost() {
        return $this->cost;
    }
    public function setCostTotal($cost) {
        $this->costTotal = $cost;
    }

    public function getCostTotal() {
        return $this->costTotal;
    }

    public function getLastop() {
        return $this->lastop;
    }

    public function setLastop($l) {
        $this->lastop = $l;
    }
// END OF GETTERS AND SETTERS

    public function solution($node) {
        $s = $node;
        do {
            $solution[] = $s->getLastop();
            $s = $s->getParent();
            } while ($s != null);
        $a = array_reverse($solution);
        array_shift($a); 

        return $a;
    }

}