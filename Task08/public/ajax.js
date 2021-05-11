function changeForm(form){
    var group_index = form.group.selectedIndex;
    var group_ = form.group.options[group.selectedIndex].value;

    var surname_index = form.surname.selectedIndex;
    var surname_ = form.surname.options[surname.selectedIndex].value;
    var course_index = form.course.selectedIndex;
    var course_ = form.course.options[course.selectedIndex].value;
    var semester_index = form.semester.selectedIndex;
    var semester_ = form.semester.options[semester.selectedIndex].value;
    var subject_index = form.subject.selectedIndex;
    var subject_ = form.subject.options[subject.selectedIndex].value;
    var mark_ = form.mark.value;
    if(group_index!=0){
        $.ajax({url: '/exam_info.php',
            method: "get",      
            dataType: "html",
            data: {
                group: group_
            },
            success:function(data) {
                $('#body').html(data);
            }       
        });
    }

    if(surname_index!=0 && course_index==0){
        $.ajax({
            url: '/exam_info.php',
            method: "get",      
            dataType: "html",
            data: {
                group: group_,
                surname: surname_
            },
            success:function(data) {
                $('#body').html(data);
            }       
        });
    }
    if(course_index!=0 && semester_index==0){
        $.ajax({
            url: '/exam_info.php',
            method: "get",      
            dataType: "html",
            data: {
                group: group_,
                surname: surname_,
                course: course_
            },
            success:function(data) {
                $('#body').html(data);
            }       
        });
    }
    if(semester_index!=0 && subject_index==0){
        $.ajax({
            url: '/exam_info.php',
            method: "get",      
            dataType: "html",
            data: {
                group: group_,
                surname: surname_,
                course: course_,
                semester: semester_
            },
            success:function(data) {
                $('#body').html(data);
            }       
        });
    }
    if(subject_index!=0 && mark_==""){
        $.ajax({
            url: '/exam_info.php',
            method: "get",      
            dataType: "html",
            data: {
                group: group_,
                surname: surname_,
                course: course_,
                semester: semester_,
                subject: subject_
            },
            success:function(data) {
                $('#body').html(data);
            }       
        });                
    }
    if(mark_!=""){
        if(/^\d*$/.test(mark_) && mark_>=0 && mark_<=100)
        {
            $.ajax({
                url: '/exam_info.php',
                method: "get",      
                dataType: "html",
                data: {
                    group: group_,
                    surname: surname_,
                    course: course_,
                    semester: semester_,
                    subject: subject_,
                    mark: mark_
                },
                success:function(data) {
                    $('#body').html(data);
                }       
            });
        } 
        else{
            alert("Неверный формат данных!");
            form.mark.value = "";
        }   
    }
}

function sendClear(){
    location.reload();
}