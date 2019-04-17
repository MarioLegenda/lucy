## Lucy
*Lightweight PHP config parser with zero dependencies*

*Lucy* requires PHP version `>=7.0 <=7.3`. Create on PHP 7.3.4

## Content

1. Introduction
2. Installation
3. Usage
4. Setting up tests

## 1. Introduction

*Lucy* is a config parser similar to Symfony config component but much more lightweight (no dependencies).
I created it because I haven't seen a competition tool for it so I created it. I don't think that Lucy is better
than Symfony config component. I just like to have more than one solution to a single problem.

*Lucy* is basically a self creating linked list. When you create the entire config tree, it creates an instances of 
itself for every node in the array (tree) and validates that part of the tree. It does that on demand so the next node 
is created only when it needs to validate the next value in the tree. I haven't done any benchmarks to compare symfony config
component and *Lucy*, but they are on their way. I hope you like it.

## 2. Installation

`composer require mario-legenda/lucy`

## 3. Usage

## 4. Setting up tests

There are no production dependencies but there are development dependencies. After you clone this repo,

`git@github.com:MarioLegenda/lucy.git`

run `composer install`. The tests are in the `/tests` directory





