    $(document).ready(function(){

        $("select").change(function(){

            $( "select option:selected").each(function(){

                if($(this).attr("value")=="protoserie"){

                    $(".box").hide();

                    $(".protoserie").show();

                }

                if($(this).attr("value")=="protoradio"){

                    $(".box").hide();

                    $(".protoradio").show();

                }

                if($(this).attr("value")=="protodallas"){

                    $(".box").hide();

                    $(".protodallas").show();

                }
                if($(this).attr("value")=="protodht"){

                    $(".box").hide();

                    $(".protodht").show();

                }
            });

        }).change();

    });
