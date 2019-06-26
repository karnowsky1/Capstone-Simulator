<!--Modal Dialog of Change Status-->

<div class="modal fade" id="changeStatus" tabindex="-1" role="dialog" aria-labelledby="changeStatus" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Change Status</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>

            <form action="staff.php" method="POST">
                <div class="modal-body">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Select Project Name</label>
                            <select class="form-control" name="projectName" required>
                                <option></option>

                                <?php
                                $result = $bus->getCapstone();
                                if($result !== FALSE){
                                    $data = $result->fetchAll();
                                    foreach($data as $project){
                                        echo "<option value ='" . $project['pid'] . "'>" . $project['name']
                                            . "</option>";
                                    }
                                }

                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Select the latest status</label>
                            <select class="form-control" name="statusChange" required>
                                <option></option>

                                <?php
                                $result = $bus->getStatus();
                                if($result !== FALSE){
                                    $data = $result->fetchAll();
                                    foreach($data as $status){
                                        echo "<option value ='" . $status['sid'] . "'>" . $status['name']
                                            . "</option>";
                                    }
                                }

                                ?>
                            </select>
                        </div>
                    </div>



                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
