<?php


use App\Models\ResponseApi;
use MrProperter\Library\PropertyConfigStructure;
use MrProperter\Models\MPModel;
use Tests\TestCase;

class XssMrpTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

    }

    public function test_ValidateString()
    {

        $model = new \MrProperter\Models\MPModel();
        $config = new PropertyConfigStructure($model);

        $prop = $config->String("companyTestName")->SetLabel("Your company")
            ->SetDescr("Ex x descr")->SetDefault("")
            ->SetPlaceholder("XX")
            ->SetLabelsWithTag("companyTestName", "Wath client company name", "OtherPlaceholder", "Other Descr")
            ->SetMin(2)->SetMax(224)
            ->AddTag(['admin', 'companyTestName']);

        $model->_propertyConfigStructure = $config;

        MPModel::saving(function () {
            return false;
        });


        // $x = Purifier::clean("<script>alert(1)</script>and any text", ['AutoFormat.AutoParagraph'=>false]);


        $cases = [
            "<script>alert(1)</script>",
            "<script>alert(1)</script>",
            "<img src=x onerror=alert(1)>",
            "<svg/onload=alert(1)>",

            // Обфускация и энкодинг
            "<script>alert(String.fromCharCode(88,83,83))</script>",
            "<img src=x onerror=\"alert&#40;1&#41;\">",
            "<img src=x onerror=\"&#97;&#108;&#101;&#114;&#116;&#40;&#49;&#41;\">",
            "<img src=x onerror=\"\u0061\u006c\u0065\u0072\u0074(1)\">",

            // Без кавычек и пробелов
            "<svg/onload=alert(1)>",
            "<img/src=x/onerror=alert(1)>",
            "<iframe src=javascript:alert(1)>",
            "<body onload=alert(1)>",

            // Case manipulation
            "<ScRiPt>alert(1)</ScRiPt>",
            "<IMG SRC=x ONERROR=alert(1)>",
            "<ImG sRc=x OnErRoR=alert(1)>",

            // NULL bytes и спецсимволы
            "<script\x00>alert(1)</script>",
            "<img src=x\x00onerror=alert(1)>",
            "<img src=x\nonerror=alert(1)>",
            "<img src=x\r\nonerror=alert(1)>",

            // Data URI
            "<img src=\"data:text/html,<script>alert(1)</script>\">",
            "<object data=\"data:text/html,<script>alert(1)</script>\">",
            "<iframe src=\"data:text/html,<script>alert(1)</script>\">",

            // JavaScript protocol
            "<a href=\"javascript:alert(1)\">Click</a>",
            "<form action=\"javascript:alert(1)\"><input type=submit>",
            "<img src=\"javascript:alert(1)\">",
            "<iframe src=\"javascript:alert(1)\">",

            // Encoded protocols
            "<a href=\"javas&#99;ript:alert(1)\">Click</a>",
            "<a href=\"jav&#x09;ascript:alert(1)\">Click</a>",
            "<a href=\"&#14;javascript:alert(1)\">Click</a>",

            // SVG vectors
            "<svg><script>alert(1)</script></svg>",
            "<svg><animate onbegin=alert(1)>",
            "<svg><set attributeName=onclick value=alert(1)>",
            "<svg><foreignObject><body onload=alert(1)></foreignObject></svg>",

            // CSS-based
            "<style>*{background:url('javascript:alert(1)')}</style>",
            "<div style=\"background:url('javascript:alert(1)')\">",
            "<div style=\"width:expression(alert(1))\">", // IE

            // HTML5 events
            "<input autofocus onfocus=alert(1)>",
            "<select autofocus onfocus=alert(1)>",
            "<textarea autofocus onfocus=alert(1)>",
            "<video><source onerror=alert(1)>",
            "<audio src=x onerror=alert(1)>",

            // Meta refresh
            "<meta http-equiv=\"refresh\" content=\"0;url=javascript:alert(1)\">",

            // Base tag
            "<base href=\"javascript:alert(1)//\">",

            // Form hijacking
            "<form><button formaction=javascript:alert(1)>Click</button></form>",
            "<input type=submit formaction=javascript:alert(1)>",

            // Mutation XSS (mXSS)
            "<noscript><p title=\"</noscript><img src=x onerror=alert(1)>\">",
            "<svg><p><style><a id=\"</style><img src=x onerror=alert(1)\">",

            // Unicode/UTF-7
            "+ADw-script+AD4-alert(1)+ADw-/script+AD4-", // UTF-7
            "＜script＞alert(1)＜/script＞", // Fullwidth

            // Template injections
            "{{constructor.constructor('alert(1)')()}}",
            '${alert(1)}',
            "#{alert(1)}",

            // Polyglot payloads
            "jaVasCript:/*-/*`/*\`/*'/*\"/**/(/* */onerror=alert(1) )//%0D%0A%0d%0a//</stYle/</titLe/</teXtarEa/</scRipt/--!>\x3csVg/<sVg/oNloAd=alert(1)//>\x3e",

            // Markdown/BBCode injection
            "[url=javascript:alert(1)]Click[/url]",
            "[img]javascript:alert(1)[/img]",

            // Double encoding
            "%3Cscript%3Ealert(1)%3C/script%3E",
            "&lt;script&gt;alert(1)&lt;/script&gt;",

            // Filter bypass tricks
            "<scr<script>ipt>alert(1)</scr</script>ipt>",
            "<<SCRIPT>alert(1);//<</SCRIPT>",
            "<script>alert(1)<!–",
            "<script>alert(1)</script",

            // Comment-based
            "<!--<img src=x onerror=alert(1)>-->",
            "<!--[if gte IE 4]><script>alert(1)</script><![endif]-->",

            // CDATA
            "<![CDATA[<script>alert(1)</script>]]>",

            // XML-based
            "<?xml version=\"1.0\"?><html:script>alert(1)</html:script>",

            // Backticks
            "<img src=`x` onerror=`alert(1)`>",

            // Alternative event handlers
            "<marquee onstart=alert(1)>",
            "<details open ontoggle=alert(1)>",
            "<dialog open onclose=alert(1)>",

            // Long attribute names
            "<img/src=x onerrrrrrrrrrrrrrrrrrrrrror=alert(1)>", // Может обойти некоторые regex

            // Newlines and tabs
            "<img\tsrc=x\tonerror=alert(1)>",
            "<img\nsrc=x\nonerror=alert(1)>",

            // Real-world combo
            "'\"><script>alert(String.fromCharCode(88,83,83))</script>",
            "<img src=x:alert(alt) onerror=eval(src) alt=1>",

            // DOM-based contexts
            "<script>location='javascript:alert(1)'</script>",
            "<script>eval(atob('YWxlcnQoMSk='))</script>", // base64: alert(1)
        ];

        $addedText = "myNameIs";



        foreach ($cases as $case) {


            $input = $addedText.$case;

            $request = [
                'companyTestName' => $addedText.$case
            ];
            $result = $model->ValidateAndFilibleByRequest($request, "admin");

            $this->assertStringNotContainsString(strtolower($case), $model->companyTestName);

            if ($result === true) {
                return ResponseApi::Successful();
            }

        }


    }


}
