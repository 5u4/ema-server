<?php

namespace Tests\Feature;

use GraphAware\Neo4j\OGM\EntityManager;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    private const TEST_USERNAME = 'Alex';
    private const TEST_EMAIL    = 'demo@sfu.ca';
    private const TEST_PASSWORD = 'password';

    /** @var EntityManager $entityManager */
    private $entityManager;
    /** @var array $headers The main demo user headers */
    private $headers = [];
    /** @var int $demoUserId The main demo user id */
    private $demoUserId;

    /**
     * @test
     */
    public function transactionTest()
    {

        $this->registerAndLogin();
        $this->createSomeExpenses();

    }

    protected function setUp()
    {
        parent::setUp();
        $this->entityManager = $this->app->make(EntityManager::class);
        $this->removeNeoDatabaseContent();
        Artisan::call('migrate:refresh', ['--seed' => true]);
    }

    /**
     * @throws \Exception
     */
    private function removeNeoDatabaseContent()
    {
        $this->entityManager->createQuery("MATCH (n) DETACH DELETE n")->execute();
    }

    /**
     * Register an user and login
     */
    private function registerAndLogin()
    {
        $registerResponse = $this->post('/api/v1/auth/register', [
            'username' => self::TEST_USERNAME,
            'email' => self::TEST_EMAIL,
            'password' => self::TEST_PASSWORD,
        ])->assertStatus(Response::HTTP_CREATED)->json();

        $this->headers['Authorization'] = "Bearer " . $registerResponse['token'];
        $this->demoUserId = $registerResponse['data']['id'];

        $this->post('/api/v1/auth/login', [
            'email' => self::TEST_EMAIL,
            'password' => self::TEST_PASSWORD
        ])->assertStatus(Response::HTTP_OK);
    }

    private function createSomeExpenses()
    {
//         foreach ($this->expenses() as $expense) {
//             $this->post('/api/v1/transaction', [
//                 'amount' => $expense['amount'],
//                 'description' => $expense['description'],
//                 'timestamp' => $expense['timestamp'],
//                 'tags' => $expense['tags'],
//             ], $this->headers)->assertStatus(Response::HTTP_CREATED);
//             sleep(0.1);
//         }
        foreach ($this->expenses() as $expense) {
            $res = $this->post('/api/v1/transaction', [
                'amount' => $expense['amount'],
                'description' => $expense['description'],
                'timestamp' => $expense['timestamp'],
                'tags' => $expense['tags'],
            ], $this->headers);
            if ($res->getStatusCode() === 500) {
                dd($res->json());
            }
        }
    }

    private function expenses(): array
    {
        $transactions = array();

        $descriptions = [
            "Dinner with friends",
            "Fast food",
            "Speeding ticket",
            "Lost money",
            "Need to buy text book"
        ];

        $tags = [
          "food",
          "party, drink",
          "school",
          "car",
          "company, work"
        ];

        for($i = 0; $i < 100; $i++) {
            $transactions[] = [
                'amount'      => number_format(lcg_value()*100,2),
                'description' => $descriptions[rand(0,4)],
                'timestamp'   => rand(1541046071, 1543551671), // Nov 1 to Nov 30, 2018
                'tags'        => $tags[rand(0,4)].rand(0,3),
            ];
        }

        return $transactions;
    }
}
