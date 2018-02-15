<?php
namespace Plokko\phpFCMv1;


use Throwable;

class BadRequest extends \Error
{
    private
        $fieldViolations;


    function __construct(array $fieldViolations,$code,Throwable $previous=null)
    {
        $description = '';
        foreach($fieldViolations AS $violation){
            $description.=($description===''?'':',').$violation['description'];
        }
        parent::__construct($description, $code, $previous);
        $this->fieldViolations = $fieldViolations;
    }

    /**
     * @return array array of { field: string, description:string }
     */
    public function getFieldViolations()
    {
        return $this->fieldViolations;
    }

    function __toString()
    {
        return 'BadRequest:'.$this->getMessage();
    }
}