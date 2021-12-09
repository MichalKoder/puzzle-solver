<?php

require_once('Node.php');
require_once('SlidingPuzzle.php');

enum Algo : string {
    case DFS = "DEPTH FIRST SEARCH";
    case GREEDY = "HEURISTIC GREEDY SEARCH";
    case ASTAR = "HEURISTIC A* SEARCH";
}

class CostsHeapAstar extends SplHeap {
    protected function compare($node1,$node2) : int{
        return ($node2->getCostTotal()<=>$node1->getCostTotal());
    }
}
class CostsHeapGreedy extends SplHeap {
    protected function compare($node1,$node2) : int{
        return ($node2->getCost()<=>$node1->getCost());
    }
}

Class PrzesuwankaSearch {

public $currentNode;
public $algo;
public $count;
    
private $przesuwanka;
private $openlist;
private $visited=[];
private $finalState;
private $initState;

public function __construct($przesuwanka = null, $stanpoczatkowy = null, Algo $algo = Algo::DFS) {
    $this->algo = $algo;
    if(isset($przesuwanka)) {
    $this->przesuwanka = $przesuwanka;
    } else if(isset($stanpoczatkowy)) {
        $this->przesuwanka = new SlidingPuzzle((int)sqrt(count($stanpoczatkowy)),$stanpoczatkowy);
    }
    if (isset($this->przesuwanka)) {
    $root = new Node(state: $this->przesuwanka->getState(), ops: [1,2,3,4], lastop: null, parent: null, depth: 0); // w konstruktorze tworzymy korzeń drzewa poszukiwań
    if($algo == Algo::DFS)  {
      $this->openlist[] = $root;

    } elseif($algo == Algo::ASTAR) {
    $this->openlist = new CostsHeapAstar();
    $this->openlist->insert($root);
    } else {
        $this->openlist = new CostsHeapGreedy();
        $this->openlist->insert($root);
    }
}
}

// Heurystyka #1 Dystans Manhattański
public function getManhattanCost($przesuwanka) : int {
$cost = 0;
$zeroInd = $przesuwanka->getGapPos();
    for($i=0;$i<$przesuwanka->getSize();$i++) {
        if($i == $zeroInd) continue;
        $y1 = (int)($i / $przesuwanka->getDim())-1; 
        $x1 = ($i % $przesuwanka->getDim())-1;
        $y0 = (int)(($przesuwanka->getState()[$i]-1) / $przesuwanka->getDim())-1;
        $x0 = ((($przesuwanka->getState()[$i])-1) % $przesuwanka->getDim())-1;
        $dx = abs($x1 - $x0);
        $dy = abs($y1 - $y0);
        $cost+=$dx+$dy;
    }
return $cost;
}

// Heurystyka #2 Dystans Hamminga
// element nie na swoim docelowym miejscu stanowi koszt = 1

public function getHammingCost($przesuwanka) : int {
$cost = 0;
for($i=0;$i<$przesuwanka->getSize();$i++) {
    if($przesuwanka->getFinalState()[$i] != $przesuwanka->getState()[$i]) 
    $cost++;
};
return $cost;
}

public function graniceGry() { 
    if(!($this->getPrzesuwanka()->getLegalLeft()[$this->getPrzesuwanka()->getGapPos()])) $this->getCurrentNode()->unsetOp(1);
    if(!($this->getPrzesuwanka()->getLegalRight()[$this->getPrzesuwanka()->getGapPos()])) $this->getCurrentNode()->unsetOp(2);
    if(!($this->getPrzesuwanka()->getLegalDown()[$this->getPrzesuwanka()->getGapPos()])) $this->getCurrentNode()->unsetOp(3);
    if(!($this->getPrzesuwanka()->getLegalUp()[$this->getPrzesuwanka()->getGapPos()])) $this->getCurrentNode()->unsetOp(4);
return $this; 
}

public function antiCycle() {
    // 1. Unikanie wyboru operatora prowadzącego do powrotu do poprzedniego stanu
    switch ($this->getCurrentNode()->getLastop()) {
        case 1 : $this->currentNode->unsetOp(2);break;
        case 2 : $this->currentNode->unsetOp(1);break;
        case 3 : $this->currentNode->unsetOp(4);break;
        case 4 : $this->currentNode->unsetOp(3);break;
    }
    return $this; 
}

public function isSolved() {
    return $this->przesuwanka->isSolved();
}

public function popOpen() {
    if(is_array($this->openlist)) {
        return array_pop($this->openlist); 
    } else {
        return $this->openlist->extract();
    }
}

public function pushOpen($node) {
    if(is_array($this->openlist)) {
        $this->openlist[] = $node; 
    } else {
        $this->openlist->insert($node);
    }
}

public function pushVisited($node) {
    $this->visited[] = $node;
}

// GETTERS AND SETTERS
public function getPrzesuwanka() {
    return $this->przesuwanka;
}

public function setCurrentNode($node) {
    $this->currentNode = $node;
}
public function getCurrentNode() {
    return $this->currentNode;
}

public function emptyOpen(){
if(is_array($this->openlist)) return empty($this->openlist);
else
return $this->openlist->isEmpty();
}

public function getOpen() {
    return $this->openlist;
}

public function getVisited() {
    return $this->visited;
}

public function setPrzesuwanka($node) {
    $this->przesuwanka->setState($node->getState());
}
//

public function search() {
$cost = 0; // inicjalizacja potrzebna dla DFS
$this->count = 0;
$notSolved = true;
    
// start pomiaru czasu działania algorytmu    
$start = microtime(1);
// wykonuj dopóki stos nie jest pusty
while((!$this->emptyOpen()) && $notSolved) {
$this->count++;

// zdejmij element z openlist i przypisz go do CurrentNode
$this->setCurrentNode($this->popOpen());
if(in_array($this->getCurrentNode()->getState(),$this->getVisited())) continue;

$this->pushVisited($this->getCurrentNode()->getState()); // dodanie obecnie "odwiedzanego" węzła do listy odwiedzonych
$this->setPrzesuwanka($this->getCurrentNode());

if($this->getPrzesuwanka()->isSolved()) {
    $notSolved = false;
}
 else {
// W przeciwnym razie:
// zawężenie listy operatorów bieżącego węzła, który za chwilę będzie rodzicem

$this->graniceGry();
$this->antiCycle();

$childDepth = $this->getCurrentNode()->getDepth()+1;

// tworzenie dzieci
for($i=0;$i<count($this->getCurrentNode()->getOps());$i++) {
    $przesuwanka = clone $this->getPrzesuwanka();
    $przesuwanka->move($this->getCurrentNode()->getOps()[$i]);
   
    // ustawienie kosztu dla dziecka
    if($this->algo == Algo::ASTAR)
    $cost = ($childDepth + $this->getManhattanCost($przesuwanka) + $this->getHammingCost($przesuwanka));
    elseif($this->algo == Algo::GREEDY)
    $cost = $this->getManhattanCost($przesuwanka) + $this->getHammingCost($przesuwanka);
    //ustawienie rodzica dla dziecka jako bieżący węzeł
    //umieszczenie dziecka na liscie open od razu w posortowanym miejscu
    $this->pushOpen(new Node(state: $przesuwanka->getState(), ops: [1,2,3,4], lastop: $this->getCurrentNode()->getOps()[$i],parent: $this->getCurrentNode(), depth: $childDepth, cost: $cost));
}

// jeśli nie ma więcej dzieci to sortuj stos
// zoptymalizowano przez wstawianie do sterty
}
} // koniec głównej pętli poszukiwań
$finish = microtime(true) - $start;
$depth = $this->currentNode->getDepth();
$visited = count($this->visited);

if($notSolved) {
    return ["time" => $finish, "type" => $this->algo->value, "depth" => $depth, "nodesTotal" => $this->count, "nodesVisited" => $visited, "solution" => []];
} else
// zwróć ścieżkę za pomocą CurrentNode
{
$solArr = $this->getCurrentNode()->solution($this->getCurrentNode());
$moves = count($solArr);
$finish = microtime(true) - $start;
return ["time" => $finish, "type" => $this->algo->value, "depth" => $depth, "nodesTotal" => $this->count, "nodesVisited" => $visited, "solution" => $solArr];

}
}

} // koniec definicji klasy


