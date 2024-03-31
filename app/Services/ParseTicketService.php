<?php

namespace Koltsova\Freshdesk\Services;

use Exception;

class ParseTicketService
{
    public array $fillable = [];
    private FreshDeskService $freshDeskService;
    private array $data;
    private array $agents;
    private array $groups;
    private array $statuses;
    private array $priorities;

    public function __construct(
        array            $headers,
        array            $data,
        array            $agents,
        array            $groups,
        array            $statuses,
        array            $priorities,
        FreshDeskService $freshDeskService
    )
    {
        $this->freshDeskService = $freshDeskService;
        $this->data = $data;
        $this->agents = $agents;
        $this->groups = $groups;
        $this->statuses = $statuses;
        $this->priorities = $priorities;

        foreach ($headers as $title => $key) {
            if (!empty($this->fillable[$title])) {
                continue;
            }

            if (is_array($key)) {
                $methodName = $key[1];
                $this->args = [$data[$key[2]]];

                call_user_func_array([$this, $methodName], $this->args);
            } else {
                $this->fillable[$title] = $data[$key];
            }
        }
    }

    private function setStatus(int $statusId)
    {
        $status = $this->statuses[$statusId];

        $this->fillable['Status'] = $status;
    }

    private function setPriority(int $priorityId)
    {
        $priority = $this->priorities[$priorityId];

        $this->fillable['Priority'] = $priority;
    }

    private function setAgent($agentId)
    {
        $agent = $this->agents[$agentId];

        if (empty($agent)) {
            $agent = $this->freshDeskService->getAgentById($agentId);
        }

        $this->fillable['Agent Name'] = $agent['contact']['name'];
        $this->fillable['Agent Email'] = $agent['contact']['email'];

    }

    private function setContact()
    {
        $contact = $this->data['requester'];

        if (empty($contact)) {
            $contact = $this->freshDeskService->getContactById($this->data['requester_id']);
        }

        $this->fillable['Contact Name'] = $contact['name'];
        $this->fillable['Contact Email'] = $contact['email'];
    }

    private function setGroup($groupId)
    {
        $group = $this->groups[$groupId];

        if (empty($group)) {
            $group = $this->freshDeskService->getGroupById($groupId);
        }

        $this->fillable['Group Name'] = $group['name'];
    }

    private function setCompany()
    {
        $company = $this->data['company'];

        if (empty($company)) {
            $company = $this->freshDeskService->getCompanyById($this->data['company_id']);
        }

        $this->fillable['Company Name'] = $company['name'];
    }

    private function setComments($ticketId)
    {
        $comments = $this->freshDeskService->getTicketConversations($ticketId);
        $commentsJson = json_encode($comments);
        $this->fillable['Comments'] = $commentsJson;
    }
}