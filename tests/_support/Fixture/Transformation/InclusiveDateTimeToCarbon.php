<?php

namespace Tests\Fixture\Transformation;

use Carbon\Carbon;
use DateTime;
use Exception;
use Metamorph\Exception\TransformException;
use MongoDB\BSON\UTCDateTime;

class InclusiveDateTimeToCarbon
{
    public function transform($datetime)
    {
        if (empty($datetime)) {
            return null;
        }
        try {
            if ($datetime instanceof UTCDateTime) {
                $datetime = $datetime->toDateTime();
            }
            if ($datetime instanceof Carbon) {
                return $datetime;
            }
            if ($datetime instanceof DateTime) {
                $carbon = new Carbon($datetime);

                return $carbon;
            }
            if (is_string($datetime)) {
                $carbon = new Carbon($datetime);

                return $carbon;
            }

            return null;
        } catch (Exception $e) {
            throw new TransformException('Failed to transform userArrayBirthday because Carbon.');
        }
    }
}