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
//        $this->add('{"table":2,"orders":{"foods":[{"name":"Pepperoni Pizza","price":10}],"drinks":[]},"bill":10}');
    }

    /**
     * @param $json
     * @throws \Everyman\Neo4j\Exception
     */
    public function add($json)
    {
        $arr = json_decode($json, true);
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
    public function addRelationship($visit, array $orders)
    {
        print_r($orders);
        foreach ($orders as $order) {
            foreach ($order as $key => $value) {
//                print_r($value['name']);
//                print 'key is ' . $key;
                $order = $this->neo4j->makeNode();

                $order->setProperty('name', $value['name'])
                ->setProperty('price', $value['price'])
                ->save();

                $visit->relateTo($order, 'ORDERED')->save();
            }
        }
        echo 'data added';
    }
}
