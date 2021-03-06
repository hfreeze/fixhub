<?php

/*
 * This file is part of Fixhub.
 *
 * Copyright (C) 2016 Fixhub.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fixhub\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Fixhub\Http\Controllers\Controller;
use Fixhub\Bus\Jobs\Repository\UpdateGitMirrorJob;
use Fixhub\Models\Command;
use Fixhub\Models\Deployment;
use Fixhub\Models\Project;
use Fixhub\Models\ServerLog;

/**
 * The controller of repository.
 */
class RepositoryController extends Controller
{

    /**
     * Handles incoming requests to refresh repository.
     *
     * @param Request $request
     * @param string  $hash
     * @param int $project_id
     *
     * @return Response
     */
    public function refresh(Request $request, $project_id)
    {
        $success = false;

        $project = Project::findOrFail($project_id);

        dispatch(new UpdateGitMirrorJob($project));

        $success = true;

        return [
            'success' => $success,
            'last_mirrored' => $project->last_mirrored->toDateTimeString(),
        ];
    }
}
