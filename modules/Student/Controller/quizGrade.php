<?php

require_once 'abstractGrades.php';
require_once '../../DB.php';

class Quiz extends AbstractGrades
{
    public $studentId;
    public $mydb;
    public $ref;
    public $subjectId;

    public function __construct(AbstractGrades $z, $subjectId)
    {
        $this->studentId = $_SESSION['loggedId'];
        $this->mydb = DB::getInstance();
        $this->ref = $z;
        $this->subjectId = $subjectId;
    }

    public function upgradeGrade()
    {
        $query = "SELECT gmv.grade, s.subject_code, sn.subject_name, gm.name, s.id
                        FROM registration r
                        JOIN registration_details rd
                        ON r.id = rd.reg_id
                        JOIN subjects s
                        ON s.id = rd.subject_id
                        JOIN grading_method_values gmv
                        ON rd.id = gmv.reg_details_id
                        JOIN grading_method gm
                        ON gmv.grading_method_id = gm.id
                        JOIN subjects_names sn
                        ON s.subjects_names_id = sn.id
                        WHERE r.student_id = $this->studentId AND gm.name = 'quiz' AND s.id = $this->subjectId";

            $queryResult = mysqli_query($this->mydb, $query);
            $grade = 0;

            $row = mysqli_fetch_assoc($queryResult);
            if($row)
                $grade = $row['grade'];
            
            $finalGrades = $this->ref->upgradeGrade();
            if(isset($finalGrades))
                $grade += $finalGrades;

            return $grade;
    }
}
?>