<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 14:08:18
 */

namespace App\Http\Orchestrators\Orchestrator\Profile;

use App\Traits\DataTablesTrait;
use Core\Profile\Domain\Contracts\ProfileDataTransformerContract;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\Factory as ViewFactory;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Exceptions\Exception;

class GetProfilesOrchestrator extends ProfileOrchestrator
{
    use DataTablesTrait;

    public function __construct(
        ProfileManagementContract $profileManagement,
        protected ViewFactory $viewFactory,
        private readonly DataTables $dataTables,
        private readonly ProfileDataTransformerContract $profileDataTransformer
    ) {
        parent::__construct($profileManagement);
        $this->setViewFactory($viewFactory);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function make(Request $request): JsonResponse
    {
        $filters = $request->input('filters', []);
        $profiles = $this->profileManagement->searchProfiles($filters);

        $dataProfiles = [];
        if ($profiles->count()) {
            /** @var Profile $item */
            foreach ($profiles as $item) {
                $dataProfiles[] = $this->profileDataTransformer->write($item)->readToShare();
            }
        }

        $collection = new Collection($dataProfiles);
        $datatable = $this->dataTables->collection($collection);
        $datatable->addColumn('tools', function (array $element) {
            return $this->retrieveMenuOptionHtml($element);
        });

        return $datatable->escapeColumns([])->toJson();
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'retrieve-profiles';
    }
}
