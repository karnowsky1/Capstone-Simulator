<!--Modal Dialog of Create Capstone-->

<div class="modal fade" id="createCapstone" tabindex="-1" role="dialog" aria-labelledby="createCapstone" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Create a new capstone</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>

            <form action="grad.php" method="POST">
                <div class="modal-body">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Type</label>
                            <input type="text" name="projectType" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Project Name</label>
                            <input type="text" name="projectName" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" rows="5" cols="50" required></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Select Start-Term</label>
                            <select class="form-control" name="startTerm" required>
                                <option></option>
                                
                                <?php
                                    $result = $bus->getTerm();
                                    if($result !== FALSE){
                                        $data = $result->fetchAll();
                                        foreach($data as $term){
                                            echo "<option value ='" . $term['term'] . "'>" . $term['term']
                                                . "</option>";
                                        }
                                    }
                                    
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Select Faculty</label>
                            <select class="form-control" name="selectFaculty" required>
                                <option></option>
                                
                                <?php
                                    $result = $bus->getFaculty("Faculty");
                                    if($result !== FALSE){
                                        $data = $result->fetchAll();
                                        foreach($data as $faculty){
                                            echo "<option value ='" . $faculty['uid'] . "'>" . $faculty['name']
                                                . "</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Expected End-Date</label>
                            <input type="date" name="expectedDate" class="form-control" required>
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
