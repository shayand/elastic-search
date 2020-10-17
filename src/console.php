<?php

use Elasticsearch\ClientBuilder;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

$elastic_host = ['host'=>'localhost'];

$console = new Application('Elastic Silex Application', 'n/a');
$console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev'));
$console->setDispatcher($app['dispatcher']);

$console
    ->register('index:create-rest')
    ->setDefinition([
        new InputOption('index','i',InputOption::VALUE_REQUIRED,'name of index','tracking')
    ])
    ->setDescription('create elastic index')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app,$elastic_host) {
        $index = $input->getOption('index');

        $guzzle = new \GuzzleHttp\Client(['base_uri' => 'http://'.$elastic_host['host'].':9200','headers' => [ 'Content-Type' => 'application/json' ]]);

        $response = $guzzle->put($index."?pretty");

        $output->writeln($response->getStatusCode());
        $output->writeln($response->getBody()->getContents());
    });

$console
    ->register('index:create-client')
    ->setDefinition([
        new InputOption('index','i',InputOption::VALUE_REQUIRED,'name of index','tracking')
    ])
    ->setDescription('set index mapping')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app,$elastic_host) {
        $index = $input->getOption('index');

        $builder = ClientBuilder::create();
        $builder->setHosts($elastic_host);
        $builder->build();

        $connection = new \Elasticsearch\Client($builder->getTransport(),$builder->getEndpoint(),$builder->getRegisteredNamespacesBuilders());

        $params = [
            'index' => $index,
            'body' => [
                'settings' => [
                    'number_of_shards' => 3,
                    'number_of_replicas' => 2
                ],
                'mappings' => [
                    'users' => [ #table name
                        'properties' => [
                            'firstname' => [
                                'type' => 'keyword'
                            ],
                            'lastname' => [
                                'type' => 'keyword'
                            ],
                            'age' => [
                                'type' => 'integer'
                            ],
                            'status' => [
                                'type' => 'short'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $ret = $connection->indices()->create($params);

        $output->writeln(print_r($ret));
    });

$console
    ->register('index:populate')
    ->setDefinition([
        new InputOption('index','i',InputOption::VALUE_REQUIRED,'name of index','tracking')
    ])
    ->setDescription('populate index')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app,$elastic_host) {
        $index = $input->getOption('index');

        $builder = ClientBuilder::create();
        $builder->setHosts($elastic_host);
        $builder->build();

        $connection = new \Elasticsearch\Client($builder->getTransport(),$builder->getEndpoint(),$builder->getRegisteredNamespacesBuilders());

        $ret = $connection->index(
            [
                'index' => $index,
                'body' => [
                    'lastname' => 'sabah',
                    'age' => 29,
                    'fisrtname' => 'hasan',
                    'status' => 1,
                ],
            ]
        );


        $output->writeln(print_r($ret));
    });

$console
    ->register('index:get')
    ->setDefinition([
        new InputOption('index','i',InputOption::VALUE_REQUIRED,'name of index','tracking')
    ])
    ->setDescription('get index')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app,$elastic_host) {
        $index = $input->getOption('index');

        $builder = ClientBuilder::create();
        $builder->setHosts($elastic_host);
        $builder->build();

        $connection = new \Elasticsearch\Client($builder->getTransport(),$builder->getEndpoint(),$builder->getRegisteredNamespacesBuilders());

        $ret = $connection->search(
            [
                'index' => $index,
                'body'  => [
                    'query' => [
                        'match' => [
                            'lastname' => 'sabah'
                        ]
                    ]
                ]
            ]
        );


        $output->writeln(print_r($ret['hits']['hits']));
    });

$console
    ->register('index:update')
    ->setDefinition([
        new InputOption('index','i',InputOption::VALUE_REQUIRED,'name of index','tracking')
    ])
    ->setDescription('update index')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app,$elastic_host) {
        $index = $input->getOption('index');

        $builder = ClientBuilder::create();
        $builder->setHosts($elastic_host);
        $builder->build();

        $connection = new \Elasticsearch\Client($builder->getTransport(),$builder->getEndpoint(),$builder->getRegisteredNamespacesBuilders());

        $ret = $connection->search(
            [
                'index' => $index,
                'body'  => [
                    'query' => [
                        'match' => [
                            'lastname' => 'sabah'
                        ]
                    ]
                ]
            ]
        );

        $id = $ret['hits']['hits'][0]['_id'];

        $updateArray = [
            'index' => $index,
            'type'  => 'user',
            'id'    => $id,
            'body'  => [
                'doc' => [
                    'firstname' => 'shayan'
                ]
            ]
        ];
        $update = $connection->update($updateArray);

        $output->writeln(print_r($update));
    });

return $console;
