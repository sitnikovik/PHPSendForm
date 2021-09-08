$("form.sendForm").unbind("submit").submit(function(e)
{
    e.preventDefault();
    let form = $(this), url = $(this).attr("action");
    if (!url) return alert("The form action is empty");

    $.post($(this).attr("action"),$(this).serialize(),function(response){
        $(form).find(".btn.submit").removeClass("disabled").removeAttr("disabled");
        response = JSON.parse(response);
        if (response.error) { alert(response.error); return false; }
        else alert(response.success);
    });
    
    return false;
})