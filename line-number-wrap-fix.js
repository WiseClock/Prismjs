function sizeLinebox(codebox)
{
    codebox.append('<div id="lnfix' + i + '"></div>');
    var lnbox = codebox.children('.line-numbers-rows')[0];
    var lnboxList = $(lnbox).children();
    var lines = codebox.html().split('\n');
    for (var j = 0; j < lines.length - 2; j++)
    {
        $('#lnfix' + i).html(lines[j]);
        var lnheight = $('#lnfix' + i).height();
        lnboxList[j].style.height = lnheight + 'px';
    };
    $('#lnfix' + i).remove();
}

function resizeBox()
{
    var codeArray = $('pre[class*="language-"] code');
    for (var i = 0; i < codeArray.length; i++)
    {
        var codeBox = codeArray[i];
        sizeLinebox($(codeBox));
    };
}

window.addEventListener('resize', function()
{
    resizeBox();
});

Prism.hooks.add('complete', function (env)
{
    if (env.code.split('\n').length > 1)
    {
        for (var i = 0; i < Prism.hooks.all.complete.length - 1; i++)
            Prism.hooks.all.complete[i](env);
        sizeLinebox($(env.element));
    }
});
