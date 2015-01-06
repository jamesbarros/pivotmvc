<?php

namespace PivotMvc\Model;

use PivotMvc\Model\BaseModel as Base;

/*
 * An overriding model for photos.
 */
class StaticSampleModel extends Base
{
    public function find($env, $endpoint, $id)
    {
        return array(
            array(
                '_id' => 1,
                'k1' => 'v11',
                'k2' => 'v12',
                'k2' => 'v12',
                'k3' => 'v12',
            ),
            array(
                '_id' => 2,
                'k1' => 'v21',
                'k2' => 'v22',
                'k2' => 'v22',
                'k3' => 'v22',
            ),
            array(
                '_id' => 3,
                'k1' => 'v31',
                'k2' => 'v32',
                'k2' => 'v32',
                'k3' => 'v32',
            ),
        );
    }
}
