<?php

namespace Koltsova\Freshdesk\Services;

use Exception;
use GuzzleHttp\Client;

class FreshDeskService
{
    private $client;

    public function __construct($domain, $apiKey)
    {
        $this->client = new Client([
            'base_uri' => "https://$domain/api/v2/",
            'auth' => [$apiKey, 'X'],
        ]);
    }

    public function getTickets(int $page = 1)
    {
        $i = 1;
        do {
            $response = $this->client->get("tickets?include=requester, company, description&per_page=100&page=$page");

            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody(), true);
            }

            if ($response->getStatusCode() == 429) {
                $seconds = $response->getHeader("Retry-After");
                sleep($seconds[0]);
            }

            $i++;
        } while ($i <= 3);

        if ($response->getStatusCode() != 200) {
            throw new Exception("Failed getting tickets page");
        }
    }

    private function getAgents(int $page = 1)
    {
        $response = $this->client->get("agents?per_page=100&page=$page");

        if ($response->getStatusCode() != 200) {
            throw new Exception("Failed getting agents");
        } else {
            return json_decode($response->getBody(), true);
        }
    }

    public function getAgentById($agentId)
    {
        $response = $this->client->get("agents/$agentId");

        if ($response->getStatusCode() != 200) {
            throw new Exception("Failed getting agent by Id");
        } else {
            return json_decode($response->getBody(), true);
        }
    }

    public function getAllAgents()
    {
        $page = 1;
        $agents = [];

        while (true) {
            $response = $this->getAgents($page);

            if (empty($response)) {
                break;
            }

            $agents = array_merge($agents, $response);
            $page++;
        }

        return array_column($agents, null, 'id');
    }

    private function getGroups(int $page = 1)
    {
        $response = $this->client->get("groups?per_page=100&page=$page");

        if ($response->getStatusCode() != 200) {
            throw new Exception("Failed getting groups");
        } else {
            return json_decode($response->getBody(), true);
        }
    }

    public function getGroupById($groupId)
    {
        $response = $this->client->get("groups/$groupId");

        if ($response->getStatusCode() != 200) {
            throw new Exception("Failed getting group by Id");
        } else {
            return json_decode($response->getBody(), true);
        }
    }

    public function getAllGroups()
    {
        $page = 1;
        $groups = [];

        while (true) {
            $response = $this->getGroups($page);

            if (empty($response)) {
                break;
            }

            $groups = array_merge($groups, $response);
            $page++;
        }

        return array_column($groups, null, 'id');
    }

    public function getContactById($contactId)
    {
        $response = $this->client->get("contacts/$contactId");

        if ($response->getStatusCode() != 200) {
            throw new Exception("Failed getting contact by Id");
        } else {
            return json_decode($response->getBody(), true);
        }

    }

    public function getCompanyById($companyId)
    {
        $response = $this->client->get("companies/$companyId");

        if ($response->getStatusCode() != 200) {
            throw new Exception("Failed getting company by Id");
        } else {
            return json_decode($response->getBody(), true);
        }
    }

    public function getTicketConversations($ticketId)
    {
        $response = $this->client->get("tickets/$ticketId/conversations");

        if ($response->getStatusCode() != 200) {
            throw new Exception("Failed getting conversations by ticket Id");
        } else {
            return json_decode($response->getBody(), true);
        }
    }
}