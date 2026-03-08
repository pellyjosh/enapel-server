@extends('master')
@section('title', 'Reports/Sales | Enapel')
@section('content')
<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">User Activity Report</h4>
                        </div><!--end col-->
                        <div class="col-auto">
                            <form class="row g-2">
                                <div class="col-auto">
                                    <a class="btn bg-primary-subtle text-primary dropdown-toggle d-flex align-items-center arrow-none"
                                        data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                                        aria-expanded="false" data-bs-auto-close="outside">
                                        <i class="iconoir-filter-alt me-1"></i> Filter
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-start">
                                        <div class="p-2">
                                            <div class="form-check mb-2">
                                                <input type="checkbox" class="form-check-input" checked id="filter-all">
                                                <label class="form-check-label" for="filter-all">
                                                    All
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" class="form-check-input" checked id="filter-one">
                                                <label class="form-check-label" for="filter-one">
                                                    New
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" class="form-check-input" checked id="filter-two">
                                                <label class="form-check-label" for="filter-two">
                                                    Active
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" checked
                                                    id="filter-three">
                                                <label class="form-check-label" for="filter-three">
                                                    Inactive
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div><!--end col-->
                            </form>
                        </div><!--end col-->
                    </div><!--end row-->
                </div><!--end card-header-->
                <div class="card-body pt-0">

                    < <table class="table datatable" id="datatable_1">
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
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Jane Smith</td>
                                <td>Supervisor</td>
                                <td>Moderator</td>
                                <td>10:10</td>
                                <td>Inactive</td>
                                <td>14:10</td>
                                <td>2024-11-10</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Emily Davis</td>
                                <td>Supervisor</td>
                                <td>Support</td>
                                <td>12:21</td>
                                <td>Active</td>
                                <td>15:07</td>
                                <td>2024-08-04</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Michael Brown</td>
                                <td>Technician</td>
                                <td>Support</td>
                                <td>10:25</td>
                                <td>Inactive</td>
                                <td>13:26</td>
                                <td>2024-11-26</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>Emily Davis</td>
                                <td>Supervisor</td>
                                <td>Moderator</td>
                                <td>11:11</td>
                                <td>Active</td>
                                <td>18:52</td>
                                <td>2024-07-14</td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>Jane Smith</td>
                                <td>Technician</td>
                                <td>Moderator</td>
                                <td>10:52</td>
                                <td>Active</td>
                                <td>17:37</td>
                                <td>2024-12-10</td>
                            </tr>
                            <tr>
                                <td>6</td>
                                <td>Chris Wilson</td>
                                <td>Manager</td>
                                <td>Moderator</td>
                                <td>8:24</td>
                                <td>Inactive</td>
                                <td>13:40</td>
                                <td>2024-01-14</td>
                            </tr>
                            <tr>
                                <td>7</td>
                                <td>John Doe</td>
                                <td>Technician</td>
                                <td>Admin</td>
                                <td>8:45</td>
                                <td>Inactive</td>
                                <td>14:18</td>
                                <td>2024-08-18</td>
                            </tr>
                            <tr>
                                <td>8</td>
                                <td>John Doe</td>
                                <td>Supervisor</td>
                                <td>Support</td>
                                <td>12:19</td>
                                <td>Inactive</td>
                                <td>18:02</td>
                                <td>2024-03-14</td>
                            </tr>
                            <tr>
                                <td>9</td>
                                <td>Michael Brown</td>
                                <td>Technician</td>
                                <td>Support</td>
                                <td>10:52</td>
                                <td>Inactive</td>
                                <td>15:10</td>
                                <td>2024-09-26</td>
                            </tr>
                            <tr>
                                <td>10</td>
                                <td>John Doe</td>
                                <td>Supervisor</td>
                                <td>Support</td>
                                <td>11:01</td>
                                <td>Inactive</td>
                                <td>13:51</td>
                                <td>2024-05-05</td>
                            </tr>
                            <tr>
                                <td>11</td>
                                <td>Michael Brown</td>
                                <td>Supervisor</td>
                                <td>Admin</td>
                                <td>10:43</td>
                                <td>Active</td>
                                <td>13:53</td>
                                <td>2024-11-28</td>
                            </tr>
                            <tr>
                                <td>12</td>
                                <td>Michael Brown</td>
                                <td>Clerk</td>
                                <td>Support</td>
                                <td>9:36</td>
                                <td>Inactive</td>
                                <td>14:41</td>
                                <td>2024-08-22</td>
                            </tr>
                            <tr>
                                <td>13</td>
                                <td>Emily Davis</td>
                                <td>Technician</td>
                                <td>Admin</td>
                                <td>12:18</td>
                                <td>Active</td>
                                <td>15:17</td>
                                <td>2024-10-18</td>
                            </tr>
                            <tr>
                                <td>14</td>
                                <td>Chris Wilson</td>
                                <td>Technician</td>
                                <td>User</td>
                                <td>11:56</td>
                                <td>Inactive</td>
                                <td>13:18</td>
                                <td>2024-05-21</td>
                            </tr>
                            <tr>
                                <td>15</td>
                                <td>John Doe</td>
                                <td>Assistant</td>
                                <td>Admin</td>
                                <td>10:44</td>
                                <td>Inactive</td>
                                <td>18:40</td>
                                <td>2024-08-17</td>
                            </tr>
                            <tr>
                                <td>16</td>
                                <td>Jane Smith</td>
                                <td>Supervisor</td>
                                <td>User</td>
                                <td>9:43</td>
                                <td>Inactive</td>
                                <td>15:19</td>
                                <td>2024-06-20</td>
                            </tr>
                            <tr>
                                <td>17</td>
                                <td>Emily Davis</td>
                                <td>Supervisor</td>
                                <td>Support</td>
                                <td>8:49</td>
                                <td>Active</td>
                                <td>14:49</td>
                                <td>2024-08-31</td>
                            </tr>
                            <tr>
                                <td>18</td>
                                <td>Michael Brown</td>
                                <td>Assistant</td>
                                <td>User</td>
                                <td>10:39</td>
                                <td>Active</td>
                                <td>15:54</td>
                                <td>2024-03-05</td>
                            </tr>
                            <tr>
                                <td>19</td>
                                <td>Michael Brown</td>
                                <td>Clerk</td>
                                <td>Support</td>
                                <td>8:38</td>
                                <td>Inactive</td>
                                <td>17:22</td>
                                <td>2024-12-09</td>
                            </tr>
                            <tr>
                                <td>20</td>
                                <td>Michael Brown</td>
                                <td>Assistant</td>
                                <td>Support</td>
                                <td>8:14</td>
                                <td>Active</td>
                                <td>14:33</td>
                                <td>2024-08-03</td>
                            </tr>
                            <tr>
                                <td>21</td>
                                <td>Michael Brown</td>
                                <td>Clerk</td>
                                <td>Admin</td>
                                <td>11:58</td>
                                <td>Active</td>
                                <td>18:03</td>
                                <td>2024-12-02</td>
                            </tr>
                            <tr>
                                <td>22</td>
                                <td>Chris Wilson</td>
                                <td>Assistant</td>
                                <td>Support</td>
                                <td>12:15</td>
                                <td>Active</td>
                                <td>14:00</td>
                                <td>2024-09-08</td>
                            </tr>
                            <tr>
                                <td>23</td>
                                <td>Jane Smith</td>
                                <td>Assistant</td>
                                <td>User</td>
                                <td>12:29</td>
                                <td>Active</td>
                                <td>16:16</td>
                                <td>2024-12-17</td>
                            </tr>
                            <tr>
                                <td>24</td>
                                <td>John Doe</td>
                                <td>Technician</td>
                                <td>Admin</td>
                                <td>12:14</td>
                                <td>Inactive</td>
                                <td>18:44</td>
                                <td>2024-09-02</td>
                            </tr>
                            <tr>
                                <td>25</td>
                                <td>John Doe</td>
                                <td>Supervisor</td>
                                <td>Support</td>
                                <td>12:48</td>
                                <td>Active</td>
                                <td>17:47</td>
                                <td>2024-01-14</td>
                            </tr>
                            <tr>
                                <td>26</td>
                                <td>John Doe</td>
                                <td>Technician</td>
                                <td>Admin</td>
                                <td>10:24</td>
                                <td>Active</td>
                                <td>18:00</td>
                                <td>2024-06-12</td>
                            </tr>
                            <tr>
                                <td>27</td>
                                <td>Chris Wilson</td>
                                <td>Supervisor</td>
                                <td>Support</td>
                                <td>11:38</td>
                                <td>Active</td>
                                <td>16:21</td>
                                <td>2024-05-12</td>
                            </tr>
                            <tr>
                                <td>28</td>
                                <td>Jane Smith</td>
                                <td>Manager</td>
                                <td>Support</td>
                                <td>9:26</td>
                                <td>Active</td>
                                <td>13:04</td>
                                <td>2024-02-09</td>
                            </tr>
                            <tr>
                                <td>29</td>
                                <td>Chris Wilson</td>
                                <td>Supervisor</td>
                                <td>Support</td>
                                <td>9:09</td>
                                <td>Inactive</td>
                                <td>17:41</td>
                                <td>2024-04-09</td>
                            </tr>
                            <tr>
                                <td>30</td>
                                <td>John Doe</td>
                                <td>Supervisor</td>
                                <td>Support</td>
                                <td>8:51</td>
                                <td>Active</td>
                                <td>17:36</td>
                                <td>2024-03-09</td>
                            </tr>
                            <tr>
                                <td>31</td>
                                <td>Michael Brown</td>
                                <td>Technician</td>
                                <td>Admin</td>
                                <td>12:18</td>
                                <td>Active</td>
                                <td>16:00</td>
                                <td>2024-03-21</td>
                            </tr>
                            <tr>
                                <td>32</td>
                                <td>Jane Smith</td>
                                <td>Supervisor</td>
                                <td>Moderator</td>
                                <td>8:40</td>
                                <td>Active</td>
                                <td>14:52</td>
                                <td>2024-07-24</td>
                            </tr>
                            <tr>
                                <td>33</td>
                                <td>Jane Smith</td>
                                <td>Supervisor</td>
                                <td>Moderator</td>
                                <td>12:51</td>
                                <td>Inactive</td>
                                <td>16:34</td>
                                <td>2024-10-09</td>
                            </tr>
                            <tr>
                                <td>34</td>
                                <td>John Doe</td>
                                <td>Manager</td>
                                <td>Support</td>
                                <td>8:44</td>
                                <td>Active</td>
                                <td>13:54</td>
                                <td>2024-10-10</td>
                            </tr>
                            <tr>
                                <td>35</td>
                                <td>Emily Davis</td>
                                <td>Manager</td>
                                <td>Admin</td>
                                <td>11:58</td>
                                <td>Active</td>
                                <td>17:17</td>
                                <td>2024-02-23</td>
                            </tr>
                            <tr>
                                <td>36</td>
                                <td>Chris Wilson</td>
                                <td>Technician</td>
                                <td>Moderator</td>
                                <td>10:19</td>
                                <td>Active</td>
                                <td>17:21</td>
                                <td>2024-12-16</td>
                            </tr>
                            <tr>
                                <td>37</td>
                                <td>John Doe</td>
                                <td>Clerk</td>
                                <td>Moderator</td>
                                <td>11:04</td>
                                <td>Active</td>
                                <td>15:52</td>
                                <td>2024-01-11</td>
                            </tr>
                            <tr>
                                <td>38</td>
                                <td>John Doe</td>
                                <td>Assistant</td>
                                <td>Moderator</td>
                                <td>12:49</td>
                                <td>Active</td>
                                <td>16:40</td>
                                <td>2024-08-31</td>
                            </tr>
                            <tr>
                                <td>39</td>
                                <td>Emily Davis</td>
                                <td>Clerk</td>
                                <td>Admin</td>
                                <td>11:14</td>
                                <td>Inactive</td>
                                <td>16:24</td>
                                <td>2024-08-21</td>
                            </tr>
                            <tr>
                                <td>40</td>
                                <td>John Doe</td>
                                <td>Assistant</td>
                                <td>Admin</td>
                                <td>8:09</td>
                                <td>Active</td>
                                <td>15:55</td>
                                <td>2024-08-15</td>
                            </tr>
                            <tr>
                                <td>41</td>
                                <td>Chris Wilson</td>
                                <td>Supervisor</td>
                                <td>Moderator</td>
                                <td>11:59</td>
                                <td>Inactive</td>
                                <td>14:18</td>
                                <td>2024-02-09</td>
                            </tr>
                            <tr>
                                <td>42</td>
                                <td>Jane Smith</td>
                                <td>Supervisor</td>
                                <td>Admin</td>
                                <td>11:03</td>
                                <td>Active</td>
                                <td>16:26</td>
                                <td>2024-12-08</td>
                            </tr>
                            <tr>
                                <td>43</td>
                                <td>Emily Davis</td>
                                <td>Assistant</td>
                                <td>Admin</td>
                                <td>8:00</td>
                                <td>Active</td>
                                <td>13:04</td>
                                <td>2024-07-16</td>
                            </tr>
                            <tr>
                                <td>44</td>
                                <td>Michael Brown</td>
                                <td>Assistant</td>
                                <td>User</td>
                                <td>11:35</td>
                                <td>Inactive</td>
                                <td>18:44</td>
                                <td>2024-03-22</td>
                            </tr>
                            <tr>
                                <td>45</td>
                                <td>John Doe</td>
                                <td>Manager</td>
                                <td>User</td>
                                <td>9:15</td>
                                <td>Inactive</td>
                                <td>15:35</td>
                                <td>2024-06-03</td>
                            </tr>
                            <tr>
                                <td>46</td>
                                <td>Chris Wilson</td>
                                <td>Supervisor</td>
                                <td>Support</td>
                                <td>8:21</td>
                                <td>Inactive</td>
                                <td>13:32</td>
                                <td>2024-10-09</td>
                            </tr>
                            <tr>
                                <td>47</td>
                                <td>Emily Davis</td>
                                <td>Assistant</td>
                                <td>Moderator</td>
                                <td>11:52</td>
                                <td>Active</td>
                                <td>13:17</td>
                                <td>2024-09-08</td>
                            </tr>
                            <tr>
                                <td>48</td>
                                <td>John Doe</td>
                                <td>Technician</td>
                                <td>User</td>
                                <td>8:24</td>
                                <td>Inactive</td>
                                <td>15:24</td>
                                <td>2024-07-11</td>
                            </tr>
                            <tr>
                                <td>49</td>
                                <td>Chris Wilson</td>
                                <td>Technician</td>
                                <td>Support</td>
                                <td>8:34</td>
                                <td>Active</td>
                                <td>16:36</td>
                                <td>2024-02-07</td>
                            </tr>
                            <tr>
                                <td>50</td>
                                <td>Jane Smith</td>
                                <td>Technician</td>
                                <td>Moderator</td>
                                <td>11:23</td>
                                <td>Inactive</td>
                                <td>16:48</td>
                                <td>2024-03-26</td>
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