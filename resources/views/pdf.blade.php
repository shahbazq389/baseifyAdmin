

                        <div class="table-container">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr role="row" class="heading">
                                    <th class="no-sort" width="2%">
                                       Id
                                    </th>
                                    <th width="5%">
                                        Name&nbsp;#
                                    </th>
                                    <th >
                                        Type_id
                                    </th>
                                    <th >
                                        Is_delete
                                    </th>
                                    <th class="no-sort">
                                        Created_at
                                    </th>
                                    <th width="12%">
                                        updated_at
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($userdata as $user)
                                <tr role="row" class="heading">
                                    <td class="no-sort" width="2%">
                                       {{$user->id}}
                                    </td>
                                    <td width="5%">
                                        {{$user->name}}
                                    </td>
                                    <td >
                                        {{$user->type_id}}
                                    </td>
                                    <td>
                                        {{$user->is_delete}}
                                    </td>
                                    <td class="no-sort">
                                        {{$user->created_at}}
                                    </td>
                                    <td width="12%">
                                        {{$user->updated_at}}
                                    </td>
                                </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

    <!-- Modal Html for Create Company start -->

