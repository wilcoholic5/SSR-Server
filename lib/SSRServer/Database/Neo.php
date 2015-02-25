<?php
namespace SSRServer\Database;

use Everyman\Neo4j\Client;
use Everyman\Neo4j\Node;

class Neo
{
    /**
     * @var Client
     */
    protected $neo4j;

    protected $visitLabel;

    public function __construct(Client $neo)
    {
        $this->neo4j = $neo;
        $this->visitLabel = $this->neo4j->makeLabel('VISIT');
        $this->add('{"table":4,"orders":{"foods":{"0":"Triple Cheeseburger","1":"Fried Chicken", "2":"Pizza", "3":"Spaghetti"},"drinks":{"0":"Sprite", "1":"Coffee", "2":"Beer"}},"bill":25}');
    }

    /**
     * @param $json
     * @throws \Everyman\Neo4j\Exception
     */
    public function add($json)
    {
        $arr = json_decode($json, true);
        print_r($arr);
        $visit = $this->neo4j->makeNode();

        $visit->setProperty('bill', $arr['bill'])
            ->setProperty('table', $arr['table'])
            ->save();

        $visit->addLabels(array($this->visitLabel));
        $this->addRelationship($visit, $arr['orders']);
    }

    /**
     * @param Node $visit
     * @param array $items
     */
    public function addRelationship($visit, array $items)
    {
        foreach ($items as $item) {
            foreach ($item as $key => $value) {
                $order = $this->neo4j->makeNode()
                ->setProperty($key, $value)
                ->save();

                $visit->relateTo($order, 'ORDERED')
                    ->save();
            }
        }
    }
}
