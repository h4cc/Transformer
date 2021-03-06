<?php


namespace Enm\Transformer\Validation\ArrayConstraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class Date
 *
 * @package Enm\TransformerBundle\Validator\Constraint
 * @author  Philipp Marien <marien@eosnewmedia.de>
 */
class NotEmptyArray extends Constraint
{

  public $message = 'The array can not be empty!"';



  /**
   * @return array
   */
  public function getRequiredOptions()
  {
    return array();
  }
}
