<?php

enum Move : int{
case LEWO = 1;
case PRAWO = 2;
case DOL = 3;
case GORA = 4;
}

class SlidingPuzzle {

private $finalState; // poszukiwany stan końcowy
private $state; // aktualny układ liczb w układance
private $size; // rozmiar układanki
private $dim; // stopień macierzy układanki
private $gapPos; // indeks luki (0) w układzie liczb
private $score; // licznik wykonanych ruchów
// granice układanki
private $legalUp=[]; // np. dla rozmiaru 3x3 [0,0,0,1,1,1,1,1,1]
private $legalDown=[]; // ustawiane w konstruktorze
private $legalLeft=[];
private $legalRight=[];

// GETTERS AND SETTERS

public function getFinalState() {
    return $this->finalState;
}
public function setFinalState($fs) {
    $this->finalState = $fs;
}

public function getState() {
    return $this->state;
}
public function setState($s) {
    $this->state = $s;
    $this->gapPos = $this->zeroPos($s);;
}

public function getSize() {
    return $this->size;
}
public function setSize($s) {
    $this->size = $s;
}

public function getDim() {
    return $this->dim;
}
public function setDim($d) {
    $this->dim = $d;
}

public function getScore() {
    return $this->score;
}
public function setScore($s) {
    $this->score = $s;
}

public function getLegalUp() {
    return $this->legalUp;
}
public function setLegalUp($l) {
    $this->legalUp = $l;
}

public function getLegalDown() {
    return $this->legalDown;
}
public function setLegalDown($ld) {
    $this->legalDown = $ld;
}

public function getLegalLeft() {
    return $this->legalLeft;
}
public function setLegalLeft($ll) {
    $this->legalLeft = $ll;
}

public function getLegalRight() {
    return $this->legalRight;
}
public function setLegalRight($lr) {
    $this->legalRight = $lr;
}

public function getGapPos() {
    return $this->gapPos;
}
public function setGapPos($gp) {
    $this->gapPos = $gp;
}

// END OF GETTERS AND SETTERS

public function __construct($dim = 3, $state = null) {
    $this->size = $dim**2;
    $this->dim = $dim;
    $this->score = 0;
    // ustawienie stanu końcowego w zależności od rozmiaru
    $this->finalState = [...range(1,$this->size-1), 0];
    
    // ustawienie stanu początkowego na losowy lub jeśli podano - na podany
    if(isset($state)) { 
        $this->state = $state;
        $this->gapPos = $this->zeroPos($state);
    }
    else {
        $this->shuffle();
        $this->gapPos = $this->size - 1;
    }
    //

    // ustawienie granic układanki w zależności od rozmiaru
        // górna granica
        for($i = 0; $i < $this->dim; $i++) $this->legalUp[] = 0;
        for($i = 0; $i < ($this->size-$this->dim); $i++) $this->legalUp[] = 1;
        // dolna granica
        for($i = 0; $i < ($this->size-$this->dim); $i++) $this->legalDown[] = 1;
        for($i = 0; $i < $this->dim; $i++) $this->legalDown[] = 0;
        // lewa banda
        for($i = 0; $i < $this->size; $i++) {
            if(($i % $this->dim) == 0) $this->legalLeft[] = 0; 
            else
            $this->legalLeft[] = 1;
        }
        // prawa banda
        for($j = 0; $j < $this->dim; $j++) {
            for($i = 0; $i < $this->dim-1; $i++) {
                $this->legalRight[] = 1;
            }
            $this->legalRight[] = 0;
        }
    ////
    } // koniec definicji konstruktora

    public function shuffle() {
        do {
        // wstępne wylosowanie tablicy liczb układanki
            do {
                $this->state = $this->finalState;
                array_pop($this->state);
                shuffle($this->state);
                array_push($this->state,0);
                // ponów losowanie jeśli układ startowy taki sam jak układ końcowy    
            } while ($this->isSolved());
        } while(!($this->isSolvable())); // sprawdzenie czy układ rozwiązywalny

            $this->gapPos = $this->zeroPos($this->state);
    }

    public function isSolvable() : bool {
        // zainicjowanie sumy kontrolnej    
        $checkSum = 0; 
        // metoda na sprawdzenie czy wylosowany układ jest rozwiązywalny
        for($j=0;$j<($this->size-2);$j++) {
            $validCheck = 0;
            for($i=$j+1;$i<($this->size-1); $i++) {
                if($this->state[$i] < $this->state[$j]) {
                    $validCheck++;
                }
                // console.log(validcheck);
            }
            $checkSum+=$validCheck;
        }
        // Jeżeli suma kontrolna parzysta to układankę da się rozwiązać
        if(!($checkSum & 1)) return true;
        return false;
    }
    
    public function isSolved() {
        return $this->state == $this->finalState;
    }

    // interfejs wykonywania przesunięć elementów układanki

    public function up() {
        if ($this->legalUp[$this->gapPos]) {
                    $this->state[$this->gapPos] = $this->state[$this->gapPos-$this->dim];
                    $this->gapPos-=$this->dim;
                    $this->state[$this->gapPos] = 0;
                    $this->score++;
           }
       }
       
    public function down() {
           if ($this->legalDown[$this->gapPos]) {
               $this->state[$this->gapPos] = $this->state[$this->gapPos+$this->dim];
               $this->gapPos+=$this->dim;
               $this->state[$this->gapPos] = 0;
               $this->score++;        
              }
       }
       
    public function left() {
           if ($this->legalLeft[$this->gapPos]) {
               $this->state[$this->gapPos] = $this->state[$this->gapPos-1];
               $this->gapPos-=1;
               $this->state[$this->gapPos] = 0;
               $this->score++;
              }
       }
       
    public function right() {
           if ($this->legalRight[$this->gapPos]) {
               $this->state[$this->gapPos] = $this->state[$this->gapPos+1];
               $this->gapPos+=1;
               $this->state[$this->gapPos] = 0;
               $this->score++;
              }
       }

       // koniec interfejsu wykonywania przesunięć
    
    public function zeroPos($tab) {
        return array_search('0',$tab);
    }
    
    // "pretty print" reprezentacji stanu układanki
       public function printState() {
        $t = '';
       for($i=0;$i<$this->size;$i++) {
            $t .= $this->state[$i];
            $t .= (($i+1) % $this->dim) ? ' ' : "\n";
        }
        echo $t;
 }

 public function move(int $move) {
    switch($move) {
        case 1 : $this->left();break;
        case 2 : $this->right();break;
        case 3 : $this->down();break;
        case 4 : $this->up();break;
    }
 }



}

