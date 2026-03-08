@extends('master')
@section('title', 'Reports/Sales | Enapel')
@section('content')
<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <h4 class="card-title">Synchronize Users</h4>
                        </div><!--end col-->

                        <div class="col-auto">
                            <a class="btn bg-primary-subtle text-primary dropdown-toggle d-flex align-items-center arrow-none"
                                data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false"
                                data-bs-auto-close="outside">
                                <i class="iconoir-filter-alt me-1"></i> Filter
                            </a>
                            <div class="dropdown-menu dropdown-menu-start">
                                <div class="p-2">
                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input" checked id="filter-all">
                                        <label class="form-check-label" for="filter-all">All</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input" checked id="filter-one">
                                        <label class="form-check-label" for="filter-one">New</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input" checked id="filter-two">
                                        <label class="form-check-label" for="filter-two">Active</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" checked id="filter-three">
                                        <label class="form-check-label" for="filter-three">Inactive</label>
                                    </div>
                                </div>
                            </div>
                        </div><!--end col-->

                        <div class="col-auto">
                            <button class="btn btn-success">
                                <i class="fas fa-sync menu-icon"></i> <span>Sync all</span>
                            </button>
                        </div><!--end col-->

                        <div class="col">
                            <div class="progress">
                                <div class="progress-bar bg-gray progress-bar-striped progress-bar-animated" role="progressbar"
                                    style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">50%
                                </div>
                            </div>
                        </div><!--end col-->

                        <div class="col-auto">
                            <i class="la la-refresh text-secondary la-spin progress-icon-spin"></i>
                        </div><!--end col-->
                    </div>><!--end row-->
                </div><!--end card-header-->
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table datatable" id="datatable_1">
                            <thead>
                                <tr style="text-align: right;">
                                    <th>Staff ID</th>
                                    <th>Name</th>
                                    <th>Designation</th>
                                    <th>Role</th>
                                    <th>Time In</th>
                                    <th>Status</th>
                                    <th>Time Out</th>
                                    <th>Date</th>
                                    <th>Sync</th>
                                    <th>Sync Status</th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr>
                                    <td>1</td>
                                    <td>Staff 1</td>
                                    <td>Designation 1</td>
                                    <td>Role 1</td>
                                    <td>9:01</td>
                                    <td>Inactive</td>
                                    <td>19:01</td>
                                    <td>2024-02-02</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>2</td>
                                    <td>Staff 2</td>
                                    <td>Designation 2</td>
                                    <td>Role 2</td>
                                    <td>10:02</td>
                                    <td>Active</td>
                                    <td>20:02</td>
                                    <td>2024-03-03</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>3</td>
                                    <td>Staff 3</td>
                                    <td>Designation 3</td>
                                    <td>Role 3</td>
                                    <td>11:03</td>
                                    <td>Inactive</td>
                                    <td>21:03</td>
                                    <td>2024-04-04</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Pending</span></td>
                                </tr>

                                <tr>
                                    <td>4</td>
                                    <td>Staff 4</td>
                                    <td>Designation 4</td>
                                    <td>Role 0</td>
                                    <td>12:04</td>
                                    <td>Active</td>
                                    <td>18:04</td>
                                    <td>2024-05-05</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>5</td>
                                    <td>Staff 5</td>
                                    <td>Designation 0</td>
                                    <td>Role 1</td>
                                    <td>13:05</td>
                                    <td>Inactive</td>
                                    <td>19:05</td>
                                    <td>2024-06-06</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>6</td>
                                    <td>Staff 6</td>
                                    <td>Designation 1</td>
                                    <td>Role 2</td>
                                    <td>14:06</td>
                                    <td>Active</td>
                                    <td>20:06</td>
                                    <td>2024-07-07</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Pending</span></td>
                                </tr>

                                <tr>
                                    <td>7</td>
                                    <td>Staff 7</td>
                                    <td>Designation 2</td>
                                    <td>Role 3</td>
                                    <td>15:07</td>
                                    <td>Inactive</td>
                                    <td>21:07</td>
                                    <td>2024-08-08</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>8</td>
                                    <td>Staff 8</td>
                                    <td>Designation 3</td>
                                    <td>Role 0</td>
                                    <td>16:08</td>
                                    <td>Active</td>
                                    <td>18:08</td>
                                    <td>2024-09-09</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>9</td>
                                    <td>Staff 9</td>
                                    <td>Designation 4</td>
                                    <td>Role 1</td>
                                    <td>17:09</td>
                                    <td>Inactive</td>
                                    <td>19:09</td>
                                    <td>2024-10-10</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Pending</span></td>
                                </tr>

                                <tr>
                                    <td>10</td>
                                    <td>Staff 10</td>
                                    <td>Designation 0</td>
                                    <td>Role 2</td>
                                    <td>18:10</td>
                                    <td>Active</td>
                                    <td>20:10</td>
                                    <td>2024-11-11</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>11</td>
                                    <td>Staff 11</td>
                                    <td>Designation 1</td>
                                    <td>Role 3</td>
                                    <td>19:11</td>
                                    <td>Inactive</td>
                                    <td>21:11</td>
                                    <td>2024-12-12</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>12</td>
                                    <td>Staff 12</td>
                                    <td>Designation 2</td>
                                    <td>Role 0</td>
                                    <td>8:12</td>
                                    <td>Active</td>
                                    <td>18:12</td>
                                    <td>2024-01-13</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Pending</span></td>
                                </tr>

                                <tr>
                                    <td>13</td>
                                    <td>Staff 13</td>
                                    <td>Designation 3</td>
                                    <td>Role 1</td>
                                    <td>9:13</td>
                                    <td>Inactive</td>
                                    <td>19:13</td>
                                    <td>2024-02-14</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>14</td>
                                    <td>Staff 14</td>
                                    <td>Designation 4</td>
                                    <td>Role 2</td>
                                    <td>10:14</td>
                                    <td>Active</td>
                                    <td>20:14</td>
                                    <td>2024-03-15</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>15</td>
                                    <td>Staff 15</td>
                                    <td>Designation 0</td>
                                    <td>Role 3</td>
                                    <td>11:15</td>
                                    <td>Inactive</td>
                                    <td>21:15</td>
                                    <td>2024-04-16</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Pending</span></td>
                                </tr>

                                <tr>
                                    <td>16</td>
                                    <td>Staff 16</td>
                                    <td>Designation 1</td>
                                    <td>Role 0</td>
                                    <td>12:16</td>
                                    <td>Active</td>
                                    <td>18:16</td>
                                    <td>2024-05-17</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>17</td>
                                    <td>Staff 17</td>
                                    <td>Designation 2</td>
                                    <td>Role 1</td>
                                    <td>13:17</td>
                                    <td>Inactive</td>
                                    <td>19:17</td>
                                    <td>2024-06-18</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>18</td>
                                    <td>Staff 18</td>
                                    <td>Designation 3</td>
                                    <td>Role 2</td>
                                    <td>14:18</td>
                                    <td>Active</td>
                                    <td>20:18</td>
                                    <td>2024-07-19</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Pending</span></td>
                                </tr>

                                <tr>
                                    <td>19</td>
                                    <td>Staff 19</td>
                                    <td>Designation 4</td>
                                    <td>Role 3</td>
                                    <td>15:19</td>
                                    <td>Inactive</td>
                                    <td>21:19</td>
                                    <td>2024-08-20</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>20</td>
                                    <td>Staff 20</td>
                                    <td>Designation 0</td>
                                    <td>Role 0</td>
                                    <td>16:20</td>
                                    <td>Active</td>
                                    <td>18:20</td>
                                    <td>2024-09-21</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>21</td>
                                    <td>Staff 21</td>
                                    <td>Designation 1</td>
                                    <td>Role 1</td>
                                    <td>17:21</td>
                                    <td>Inactive</td>
                                    <td>19:21</td>
                                    <td>2024-10-22</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Pending</span></td>
                                </tr>

                                <tr>
                                    <td>22</td>
                                    <td>Staff 22</td>
                                    <td>Designation 2</td>
                                    <td>Role 2</td>
                                    <td>18:22</td>
                                    <td>Active</td>
                                    <td>20:22</td>
                                    <td>2024-11-23</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>23</td>
                                    <td>Staff 23</td>
                                    <td>Designation 3</td>
                                    <td>Role 3</td>
                                    <td>19:23</td>
                                    <td>Inactive</td>
                                    <td>21:23</td>
                                    <td>2024-12-24</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>24</td>
                                    <td>Staff 24</td>
                                    <td>Designation 4</td>
                                    <td>Role 0</td>
                                    <td>8:24</td>
                                    <td>Active</td>
                                    <td>18:24</td>
                                    <td>2024-01-25</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Pending</span></td>
                                </tr>

                                <tr>
                                    <td>25</td>
                                    <td>Staff 25</td>
                                    <td>Designation 0</td>
                                    <td>Role 1</td>
                                    <td>9:25</td>
                                    <td>Inactive</td>
                                    <td>19:25</td>
                                    <td>2024-02-26</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>26</td>
                                    <td>Staff 26</td>
                                    <td>Designation 1</td>
                                    <td>Role 2</td>
                                    <td>10:26</td>
                                    <td>Active</td>
                                    <td>20:26</td>
                                    <td>2024-03-27</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>27</td>
                                    <td>Staff 27</td>
                                    <td>Designation 2</td>
                                    <td>Role 3</td>
                                    <td>11:27</td>
                                    <td>Inactive</td>
                                    <td>21:27</td>
                                    <td>2024-04-28</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Pending</span></td>
                                </tr>

                                <tr>
                                    <td>28</td>
                                    <td>Staff 28</td>
                                    <td>Designation 3</td>
                                    <td>Role 0</td>
                                    <td>12:28</td>
                                    <td>Active</td>
                                    <td>18:28</td>
                                    <td>2024-05-01</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>29</td>
                                    <td>Staff 29</td>
                                    <td>Designation 4</td>
                                    <td>Role 1</td>
                                    <td>13:29</td>
                                    <td>Inactive</td>
                                    <td>19:29</td>
                                    <td>2024-06-02</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>30</td>
                                    <td>Staff 30</td>
                                    <td>Designation 0</td>
                                    <td>Role 2</td>
                                    <td>14:30</td>
                                    <td>Active</td>
                                    <td>20:30</td>
                                    <td>2024-07-03</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Pending</span></td>
                                </tr>

                                <tr>
                                    <td>31</td>
                                    <td>Staff 31</td>
                                    <td>Designation 1</td>
                                    <td>Role 3</td>
                                    <td>15:31</td>
                                    <td>Inactive</td>
                                    <td>21:31</td>
                                    <td>2024-08-04</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>32</td>
                                    <td>Staff 32</td>
                                    <td>Designation 2</td>
                                    <td>Role 0</td>
                                    <td>16:32</td>
                                    <td>Active</td>
                                    <td>18:32</td>
                                    <td>2024-09-05</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>33</td>
                                    <td>Staff 33</td>
                                    <td>Designation 3</td>
                                    <td>Role 1</td>
                                    <td>17:33</td>
                                    <td>Inactive</td>
                                    <td>19:33</td>
                                    <td>2024-10-06</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Pending</span></td>
                                </tr>

                                <tr>
                                    <td>34</td>
                                    <td>Staff 34</td>
                                    <td>Designation 4</td>
                                    <td>Role 2</td>
                                    <td>18:34</td>
                                    <td>Active</td>
                                    <td>20:34</td>
                                    <td>2024-11-07</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>35</td>
                                    <td>Staff 35</td>
                                    <td>Designation 0</td>
                                    <td>Role 3</td>
                                    <td>19:35</td>
                                    <td>Inactive</td>
                                    <td>21:35</td>
                                    <td>2024-12-08</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>36</td>
                                    <td>Staff 36</td>
                                    <td>Designation 1</td>
                                    <td>Role 0</td>
                                    <td>8:36</td>
                                    <td>Active</td>
                                    <td>18:36</td>
                                    <td>2024-01-09</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Pending</span></td>
                                </tr>

                                <tr>
                                    <td>37</td>
                                    <td>Staff 37</td>
                                    <td>Designation 2</td>
                                    <td>Role 1</td>
                                    <td>9:37</td>
                                    <td>Inactive</td>
                                    <td>19:37</td>
                                    <td>2024-02-10</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>38</td>
                                    <td>Staff 38</td>
                                    <td>Designation 3</td>
                                    <td>Role 2</td>
                                    <td>10:38</td>
                                    <td>Active</td>
                                    <td>20:38</td>
                                    <td>2024-03-11</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>39</td>
                                    <td>Staff 39</td>
                                    <td>Designation 4</td>
                                    <td>Role 3</td>
                                    <td>11:39</td>
                                    <td>Inactive</td>
                                    <td>21:39</td>
                                    <td>2024-04-12</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Pending</span></td>
                                </tr>

                                <tr>
                                    <td>40</td>
                                    <td>Staff 40</td>
                                    <td>Designation 0</td>
                                    <td>Role 0</td>
                                    <td>12:40</td>
                                    <td>Active</td>
                                    <td>18:40</td>
                                    <td>2024-05-13</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>41</td>
                                    <td>Staff 41</td>
                                    <td>Designation 1</td>
                                    <td>Role 1</td>
                                    <td>13:41</td>
                                    <td>Inactive</td>
                                    <td>19:41</td>
                                    <td>2024-06-14</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>42</td>
                                    <td>Staff 42</td>
                                    <td>Designation 2</td>
                                    <td>Role 2</td>
                                    <td>14:42</td>
                                    <td>Active</td>
                                    <td>20:42</td>
                                    <td>2024-07-15</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Pending</span></td>
                                </tr>

                                <tr>
                                    <td>43</td>
                                    <td>Staff 43</td>
                                    <td>Designation 3</td>
                                    <td>Role 3</td>
                                    <td>15:43</td>
                                    <td>Inactive</td>
                                    <td>21:43</td>
                                    <td>2024-08-16</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>44</td>
                                    <td>Staff 44</td>
                                    <td>Designation 4</td>
                                    <td>Role 0</td>
                                    <td>16:44</td>
                                    <td>Active</td>
                                    <td>18:44</td>
                                    <td>2024-09-17</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>45</td>
                                    <td>Staff 45</td>
                                    <td>Designation 0</td>
                                    <td>Role 1</td>
                                    <td>17:45</td>
                                    <td>Inactive</td>
                                    <td>19:45</td>
                                    <td>2024-10-18</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Pending</span></td>
                                </tr>

                                <tr>
                                    <td>46</td>
                                    <td>Staff 46</td>
                                    <td>Designation 1</td>
                                    <td>Role 2</td>
                                    <td>18:46</td>
                                    <td>Active</td>
                                    <td>20:46</td>
                                    <td>2024-11-19</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>47</td>
                                    <td>Staff 47</td>
                                    <td>Designation 2</td>
                                    <td>Role 3</td>
                                    <td>19:47</td>
                                    <td>Inactive</td>
                                    <td>21:47</td>
                                    <td>2024-12-20</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>48</td>
                                    <td>Staff 48</td>
                                    <td>Designation 3</td>
                                    <td>Role 0</td>
                                    <td>8:48</td>
                                    <td>Active</td>
                                    <td>18:48</td>
                                    <td>2024-01-21</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Pending</span></td>
                                </tr>

                                <tr>
                                    <td>49</td>
                                    <td>Staff 49</td>
                                    <td>Designation 4</td>
                                    <td>Role 1</td>
                                    <td>9:49</td>
                                    <td>Inactive</td>
                                    <td>19:49</td>
                                    <td>2024-02-22</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                                <tr>
                                    <td>50</td>
                                    <td>Staff 50</td>
                                    <td>Designation 0</td>
                                    <td>Role 2</td>
                                    <td>10:50</td>
                                    <td>Active</td>
                                    <td>20:50</td>
                                    <td>2024-03-23</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
</div><!-- container -->

@endsection
@section('body_script')

<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/libs/simple-datatables/umd/simple-datatables.js') }}"></script>
<script src="{{ asset('assets/js/pages/datatable.init.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>
@endsection