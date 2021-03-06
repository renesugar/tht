<?php

namespace tht5ae8a287f36e5;
\o\Runtime::setNameSpace('pages/home.tht','tht5ae8a287f36e5');

function u_main ()  {
 $u_test = \o\Runtime::getModule(__NAMESPACE__, 'Test')->u_new();
u_run($u_test);
\o\Runtime::getModule(__NAMESPACE__, 'Web')->u_send_html(u_html(\o\v($u_test)->u_results_html()));
 return \o\Runtime::void(__METHOD__);
}
function u_html ($u_results)  {
$t = \o\Runtime::openTemplate("html");
$t->addStatic("<!-- this is a comment --><html><head><title>THT Unit Tests</title>");
$t->addDynamic(\o\Runtime::getModule(__NAMESPACE__, 'Css')->u_include("base"));
$t->addStatic("</head><body><main><h1>THT Unit Tests</h1><a href=\"#test-results\">Skip to Results</a>");
$t->addDynamic($u_results);
$t->addStatic("</main></body></html>");
\o\Runtime::closeTemplate();
return $t->getString();
}
function u_run ($u_t)  {
 u_test_math_and_logic($u_t);
u_test_strings($u_t);
u_test_control_flow($u_t);
u_test_lists($u_t);
u_test_maps($u_t);
u_test_functions($u_t);
u_test_types($u_t);
u_test_misc($u_t);
u_test_templates($u_t);
u_runtime_errors($u_t);
u_compile_errors($u_t);
u_lib_file($u_t);
u_lib_date($u_t);
u_lib_db($u_t);
u_lib_jcon_test($u_t);
u_lib_js($u_t);
u_lib_json($u_t);
u_lib_litemark($u_t);
u_lib_math($u_t);
u_lib_perf($u_t);
u_lib_php($u_t);
u_lib_web($u_t);
u_lib_global($u_t);
u_lib_settings($u_t);
u_lib_session($u_t);
u_lib_cache($u_t);
 return \o\Runtime::void(__METHOD__);
}
function u_runtime_errors ($u_t)  {
 \o\v($u_t)->u_section("Runtime Errors");
\o\v($u_t)->u_dies(function  ()  {
 \o\v("abc")->u_sdf();
 return \o\Runtime::void(__METHOD__);
}
, "non-existent method");
\o\v($u_t)->u_dies(function  ()  {
 \o\v("abc {1}")->u_fill(\o\OList::create([ "foo" ]));
 return \o\Runtime::void(__METHOD__);
}
, "bad fill value");
\o\v($u_t)->u_dies(function  ()  {
 \o\v(\o\OMap::create([ 'a' => 1 ]))->u_sdfsdf();
 return \o\Runtime::void(__METHOD__);
}
, "invalid method");
\o\v($u_t)->u_dies(function  ()  {
 $u_a = \o\v("sdf")->u_reverse;
 return \o\Runtime::void(__METHOD__);
}
, "missing parens in method call");
$u_fun_for = function  ()  {
 foreach (\o\uv(2) as $u_foo) {

}
 return \o\Runtime::void(__METHOD__);
}
;
\o\v($u_t)->u_dies($u_fun_for, "Invalid argument");
\o\v($u_t)->u_dies(function  ()  {
 return \o\v("abc")->u_length;
 return \o\Runtime::void(__METHOD__);
}
, "length()");
 return \o\Runtime::void(__METHOD__);
}
function u_compile_errors ($u_t)  {
 \o\v($u_t)->u_section("Parser");
$u_code = "// test comments

/*
    this is a block comment
*/

let commented = 2; // line-end comment";
\o\v($u_t)->u_parser_ok($u_code, "comments");
$u_long_comment = \o\Runtime::concat(\o\Runtime::concat("// ", \o\Runtime::getModule(__NAMESPACE__, 'String')->u_repeat("a", 102)), "\n");
\o\v($u_t)->u_parser_ok($u_long_comment, "line comment over 100 chars");
$u_long_block_comment = \o\Runtime::concat(\o\Runtime::concat("/*\n", \o\Runtime::getModule(__NAMESPACE__, 'String')->u_repeat("a", 102)), "\n*/");
\o\v($u_t)->u_parser_ok($u_long_block_comment, "block comment over 100 chars");
\o\v($u_t)->u_section("Parser Errors - Names");
\o\v($u_t)->u_parser_error("let FOO = 3;", "camelCase");
\o\v($u_t)->u_parser_error("let fOO = 3;", "camelCase");
\o\v($u_t)->u_parser_error("let XMLreader = {};", "camelCase");
\o\v($u_t)->u_parser_error("let a_b = 3;", "camelCase");
\o\v($u_t)->u_parser_error("function FOO() {}", "camelCase");
\o\v($u_t)->u_parser_error("function a () {}", "longer than 1");
$u_long_name = \o\Runtime::getModule(__NAMESPACE__, 'String')->u_repeat("a", 41);
\o\v($u_t)->u_parser_error(\o\Runtime::concat(\o\Runtime::concat("let ", $u_long_name), " = 1;"), "40 characters or less");
\o\v($u_t)->u_parser_error(\o\Runtime::concat(\o\Runtime::concat("function ", $u_long_name), " () {}"), "40 characters or less");
\o\v($u_t)->u_section("Parser Errors - Aliases");
\o\v($u_t)->u_parser_error("var a = 3;", "try: `let`");
\o\v($u_t)->u_parser_error("foreach (ary as a) { }", "try: `for`");
\o\v($u_t)->u_parser_error("let ary = [];\nfor (ary as a) { }", "item in list");
\o\v($u_t)->u_parser_error("\$foo = 123", "remove \$ from name");
\o\v($u_t)->u_section("Parser Errors - Misc");
\o\v($u_t)->u_parser_error("asdasd", "unknown variable", "");
\o\v($u_t)->u_parser_error("if (a = 3) { }", "assignment", "if, missing paren");
\o\v($u_t)->u_parser_error("break;\nlet a = 3;", "unreachable");
\o\v($u_t)->u_parser_ok("return;\nlet a = 3;", "may return early");
\o\v($u_t)->u_parser_ok("if (true) { break; }", "newline not needed for one-line if");
\o\v($u_t)->u_parser_ok("function foo() { return 1; }", "newline not needed for one-line fun");
\o\v($u_t)->u_parser_error("let a = 'hello", "unexpected newline");
\o\v($u_t)->u_parser_error("for (a) {}", "expected 'in'");
\o\v($u_t)->u_parser_ok("\t let a\t=\t1;", "tabs");
\o\v($u_t)->u_parser_error("for (let i = 0; i < 10; i++) {}", "unexpected 'let'");
\o\v($u_t)->u_parser_error("1 ? 2 ? 3 : 4 : 5", "nested ternary");
\o\v($u_t)->u_parser_error("let a = E'foo';", "string modifier");
\o\v($u_t)->u_parser_error("let a = l'foo';", "uppercase");
\o\v($u_t)->u_parser_error("let a == 123;", "expected '='");
\o\v($u_t)->u_parser_error("let a;", "expected '='");
\o\v($u_t)->u_parser_error("if (2 => 1) { }", ">=");
\o\v($u_t)->u_parser_error("if (1 =< 2) { }", "<=");
\o\v($u_t)->u_parser_error("let a = 1 <> 2;", "!=");
\o\v($u_t)->u_parser_error("function foo();", "Unexpected semicolon");
\o\v($u_t)->u_parser_error("if (a == 3) .", "Expected '{'");
\o\v($u_t)->u_parser_error("function foo(),", "Unexpected comma");
\o\v($u_t)->u_parser_error("let a = { FOO: 'bar' };", "camelCase");
\o\v($u_t)->u_parser_error("let a = { foo: 'bar', foo: 1 };", "duplicate key");
\o\v($u_t)->u_parser_error("if (true) return;", "Expected '{'");
\o\v($u_t)->u_parser_error("print('a'), print('b');", "comma");
\o\v($u_t)->u_parser_error("let a = 1, b = 2;", "Missing semicolon", "");
\o\v($u_t)->u_parser_error("let a = (1 + );", "incomplete");
\o\v($u_t)->u_parser_error("let a = 2 + (1 * ) + 1;", "incomplete");
\o\v($u_t)->u_parser_error("<?", "Unexpected symbol '<'");
\o\v($u_t)->u_parser_error("?>", "Unexpected symbol '?'");
\o\v($u_t)->u_parser_error("'hello'[] = 'a';", "Assignment can not");
\o\v($u_t)->u_section("Parser Errors - Adjacent Tokens");
\o\v($u_t)->u_parser_error("let a = foo foo;", "unexpected word");
\o\v($u_t)->u_parser_error("let a = 'foo' foo;", "unexpected word");
\o\v($u_t)->u_parser_error("let a = 123 foo;", "unexpected word");
\o\v($u_t)->u_parser_error("let a = foo 'foo';", "unexpected string");
\o\v($u_t)->u_parser_error("let a = 123 'foo';", "unexpected string");
\o\v($u_t)->u_parser_error("let a = foo 123;", "unexpected number");
\o\v($u_t)->u_parser_error("let a = 'foo' 123;", "unexpected number");
\o\v($u_t)->u_parser_error("let a = [1, 2 3]", "unexpected number");
\o\v($u_t)->u_parser_error("let a = { k: a, b c }", "unexpected word");
\o\v($u_t)->u_section("Parser Errors - Newlines");
\o\v($u_t)->u_parser_error("let a = ''' sdf", "newline");
\o\v($u_t)->u_parser_error("let a = '''\ndfg ''';", "triple-quote");
\o\v($u_t)->u_parser_error("let a = 1; /*\n", "separate line");
\o\v($u_t)->u_parser_error("/*\nsdf\n*/ d", "missing newline");
\o\v($u_t)->u_parser_error("/*\nsdf", "unclosed comment");
\o\v($u_t)->u_parser_error("template fooText() {\n};", "missing newline");
\o\v($u_t)->u_section("Parser Errors - Spaces");
\o\v($u_t)->u_parser_error("function(){}", "space after 'function'");
\o\v($u_t)->u_parser_error("function foo () {}", "space before '('");
\o\v($u_t)->u_parser_error("function foo(){}", "space after ')'");
\o\v($u_t)->u_parser_error("function foo{}", "space before '{'");
\o\v($u_t)->u_parser_error("function (){}", "space after ')'");
\o\v($u_t)->u_parser_error("a = function() {};", "space after 'function'");
\o\v($u_t)->u_parser_error("F foo() {a = 1 }", "space after '{'");
\o\v($u_t)->u_parser_error("( a + 1)", "space after '('");
\o\v($u_t)->u_parser_ok("let a = (\n1 +\n2\n);", "space after '('");
\o\v($u_t)->u_parser_error("foo( );", "space after '('");
\o\v($u_t)->u_parser_error("let a = [ ]", "space after '['");
\o\v($u_t)->u_parser_error("let a = { }", "space after '{'");
\o\v($u_t)->u_parser_ok("let a = [\n];", "space after '['");
\o\v($u_t)->u_parser_ok("let a = {\n};", "space after '{'");
\o\v($u_t)->u_parser_error("let a = b[ 0]", "space after '['");
\o\v($u_t)->u_parser_error("let a = b[0 ]", "space before ']'");
\o\v($u_t)->u_parser_error("let a= 1+2;", "space before '='");
\o\v($u_t)->u_parser_error("let a =1+2;", "space after '='");
\o\v($u_t)->u_parser_error("let a = 1+ 2;", "space before '+'");
\o\v($u_t)->u_parser_error("let a = 1 +2;", "space after '+'");
\o\v($u_t)->u_parser_error("let a = {a:'b'}", "space after '{'");
\o\v($u_t)->u_parser_error("let a = { a:'b'}", "space after ':'");
\o\v($u_t)->u_parser_error("let a = { a : 'b'}", "space before ':'");
\o\v($u_t)->u_parser_error("let a = [a,b,c]", "space after ','");
\o\v($u_t)->u_parser_error("if(true) {}", "space after 'if'");
\o\v($u_t)->u_parser_error("if ( true) {}", "space after '('");
\o\v($u_t)->u_parser_error("if (true){}", "space after ')'");
\o\v($u_t)->u_parser_error("return(a);", "space after 'return'");
\o\v($u_t)->u_parser_error("a,b,c", "space after ','");
\o\v($u_t)->u_parser_error("return a ;", "space before ';'");
\o\v($u_t)->u_parser_error("a? 1 : 2;", "space before '?'");
\o\v($u_t)->u_parser_error("a ?1 : 2;", "space after '?'");
\o\v($u_t)->u_parser_error("a ? 1: 2;", "space before ':'");
\o\v($u_t)->u_parser_error("a ? 1 :2;", "space after ':'");
\o\v($u_t)->u_parser_error("let a = 1;let a = 2;", "space after ';'");
\o\v($u_t)->u_parser_ok("if (true)\n{ }", "newline after ')'");
\o\v($u_t)->u_parser_ok("else if (true)\n{ }", "newline after ')'");
\o\v($u_t)->u_parser_ok("if (true) {\n}\nelse\n{ }", "newline after 'else'");
\o\v($u_t)->u_parser_ok("for (a in ['a'])\n{ }", "newline after ')'");
\o\v($u_t)->u_parser_ok("function fn()\n{ }", "newline after ')'");
\o\v($u_t)->u_section("Parser Errors - Templates");
\o\v($u_t)->u_parser_error("template fHtml() {<", "newline");
\o\v($u_t)->u_parser_error("template fHtml() {\n  ::for", "space after '::'");
\o\v($u_t)->u_parser_error("template fHtml() {\n  :: for", "must end");
\o\v($u_t)->u_parser_error("template fHtml() {\n  {{5 }}\n}\n", "space after '{{'");
\o\v($u_t)->u_parser_error("template fHtml() {\n  {{ 5}}\n}\n", "space before '}}'");
\o\v($u_t)->u_parser_error("template fHtml() {\n  {{ \n5 }}", "unexpected newline");
\o\v($u_t)->u_parser_error("template fHtml() {\n  <hr>", "self-closing");
\o\v($u_t)->u_parser_error("template fHtml() {\n  <b>Hi</div>", "expected '</b>'");
\o\v($u_t)->u_parser_error("template fHtml() {\n  <b>Hi</b></b>", "extra closing tag");
\o\v($u_t)->u_parser_error("function fHtml() {\n  <", "unexpected '<'", "");
\o\v($u_t)->u_parser_error("template foo() {\n", "missing type");
\o\v($u_t)->u_section("Parser Errors - Assignment as Expression");
\o\v($u_t)->u_parser_error("let b = 1;\nlet a = b = 3;", "assignment can not");
\o\v($u_t)->u_parser_error("let b = 1;\nlet a = b += 3;", "assignment can not");
\o\v($u_t)->u_parser_error("let a = { b: c = 1 }", "assignment can not");
\o\v($u_t)->u_parser_error("print(a = 3);", "assignment can not");
\o\v($u_t)->u_parser_error("a[b = 3]", "assignment can not");
\o\v($u_t)->u_parser_error("for (b in a = 3) {}", "assignment can not");
\o\v($u_t)->u_parser_error("if (a = 3) {}", "assignment can not");
\o\v($u_t)->u_parser_error("F foo() { return a = 3;\n }", "assignment can not");
\o\v($u_t)->u_section("Parser Errors - Scope");
\o\v($u_t)->u_parser_error("a = 3;", "unknown variable");
\o\v($u_t)->u_parser_error("let a = 1;\nlet a = 2;", "already defined");
\o\v($u_t)->u_parser_error("let fOo = 1;\nlet foO = 2;", "already defined");
\o\v($u_t)->u_parser_error("let a = 1;\nif (a == 1) {\n let a = 2;\n}", "already defined");
\o\v($u_t)->u_parser_error("if (true) {\n let a = 1;\n let a = 2;\n}", "already defined");
\o\v($u_t)->u_parser_ok("if (true) {\n let a = 1; }\nif (true) { let a = 2;\n }", "already defined");
\o\v($u_t)->u_parser_error("let a = 1;\nfunction foo(a) {}", "already defined");
\o\v($u_t)->u_parser_error("function foo(a, a) {}", "already defined");
\o\v($u_t)->u_parser_error("function foo(a = 1, a) {}", "already defined");
\o\v($u_t)->u_parser_error("function foo() { }\nfunction foo() { }", "already defined");
\o\v($u_t)->u_parser_error("function foo() { }\nfunction fOo() { }", "already defined");
\o\v($u_t)->u_parser_error("let a = 1;\nfor (a in ary) {}", "already defined");
\o\v($u_t)->u_parser_error("let print = 123;", "core function");
\o\v($u_t)->u_parser_error("let finally = 123;", "reserved");
\o\v($u_t)->u_parser_error("function foo() keep (a) { }", "unknown variable");
\o\v($u_t)->u_parser_error("b = 4;", "unknown variable");
\o\v($u_t)->u_parser_error("let fOo = 1;\nfoO = 2;", "unknown variable");
\o\v($u_t)->u_parser_error("let a = a + 1;", "unknown variable");
\o\v($u_t)->u_parser_error("function foo() { }\nfOo();", "case mismatch", "");
 return \o\Runtime::void(__METHOD__);
}
function u_test_misc ($u_t)  {
 \o\v($u_t)->u_section("Performance");
\o\Runtime::getModule(__NAMESPACE__, 'Perf')->u_start("Large Array");
$u_now = \o\Runtime::getModule(__NAMESPACE__, 'Date')->u_now(true);
$u_num_els = 1000;
$u_nums = \o\OBare::u_range(1, $u_num_els);
$u_ii = 0;
foreach (\o\uv($u_nums) as $u_nn) {
$u_b = \o\v($u_nums)[$u_ii];
$u_ii += \o\vn(1, 1);

}
\o\v($u_t)->u_ok(($u_ii === $u_num_els), "large loop done");
$u_elapsed = (\o\vn(\o\Runtime::getModule(__NAMESPACE__, 'Date')->u_now(true), 0) - \o\vn($u_now, 0));
\o\v($u_t)->u_ok((\o\vn($u_elapsed, 0) < \o\vn(3, 0)), \o\v("ArrayAccess loop ({0} elements) took {1} ms")->u_fill($u_num_els, $u_elapsed));
\o\Runtime::getModule(__NAMESPACE__, 'Perf')->u_stop();
\o\v($u_t)->u_section("Functional Methods");
\o\v($u_t)->u_section("Result Objects");
$u_st = \o\Runtime::getModule(__NAMESPACE__, 'Result')->u_ok(123);
\o\v($u_t)->u_ok(\o\v($u_st)->u_ok(), "not ok");
\o\v($u_t)->u_ok((\o\v($u_st)->u_get() === 123), "ok value");
$u_st = \o\Runtime::getModule(__NAMESPACE__, 'Result')->u_fail(66);
\o\v($u_t)->u_ok((! \o\v($u_st)->u_ok()), "not ok");
\o\v($u_t)->u_ok((\o\v($u_st)->u_fail_code() === 66), "failCode");
\o\v($u_t)->u_section("Modules");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'TestModule')->u_bare_fun("Joe") === "bare:Joe"), "module call - autoloaded");
\o\Runtime::getModule(__NAMESPACE__, 'Global')->u_foo = "BAR";
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'TestModule')->u_test_global() === "global:BAR"), "module global");
\o\OBare::u_import(__NAMESPACE__, "subDir/OtherModule");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'OtherModule')->u_ok("Joe") === "ok:Joe"), "import from subfolder");
 return \o\Runtime::void(__METHOD__);
}
function u_test_types ($u_t)  {
 \o\v($u_t)->u_section("Types");
\o\v($u_t)->u_ok(\o\v(\o\OList::create([  ]))->u_is_list(), "list");
\o\v($u_t)->u_ok(\o\v(\o\OMap::create([  ]))->u_is_map(), "map");
\o\v($u_t)->u_ok(\o\v("foo")->u_is_string(), "string");
$u_n = 123;
\o\v($u_t)->u_ok(\o\v($u_n)->u_is_number(), "number");
$u_f = true;
\o\v($u_t)->u_ok(\o\v($u_f)->u_is_flag(), "flag");
$u_fn = function  ()  {
  return \o\Runtime::void(__METHOD__);
}
;
\o\v($u_t)->u_ok(\o\v($u_fn)->u_is_function(), "function");
\o\v($u_t)->u_section("Empty Values");
\o\v($u_t)->u_ok(\o\v(\o\OList::create([  ]))->u_is_empty(), "empty list");
\o\v($u_t)->u_ok(\o\v(\o\OMap::create([  ]))->u_is_empty(), "empty map");
\o\v($u_t)->u_ok(\o\v("")->u_is_empty(), "empty string");
$u_n = 0;
\o\v($u_t)->u_ok(\o\v($u_n)->u_is_empty(), "empty num");
$u_f = false;
\o\v($u_t)->u_ok(\o\v($u_f)->u_is_empty(), "empty flag");
\o\v($u_t)->u_ok((! \o\v(\o\OList::create([ 1, 2, 3 ]))->u_is_empty()), "non-empty list");
\o\v($u_t)->u_ok((! \o\v(\o\OMap::create([ 'foo' => 0 ]))->u_is_empty()), "non-empty map");
\o\v($u_t)->u_ok((! \o\v("abc")->u_is_empty()), "non-empty string");
$u_n = 0.1;
\o\v($u_t)->u_ok((! \o\v($u_n)->u_is_empty()), "non-empty num");
$u_f = true;
\o\v($u_t)->u_ok((! \o\v($u_f)->u_is_empty()), "non-empty flag");
 return \o\Runtime::void(__METHOD__);
}
function u_test_functions ($u_t)  {
 \o\v($u_t)->u_section("Functions");
function u_test ()  {
 return "yay";
 return \o\Runtime::void(__METHOD__);
}
\o\v($u_t)->u_ok((u_test() === "yay"), "no args");
function u_test_a ($u_arg)  {
 return \o\Runtime::concat($u_arg, "!");
 return \o\Runtime::void(__METHOD__);
}
\o\v($u_t)->u_ok((u_test_a("hey") === "hey!"), "with arg");
function u_test_b ($u_arg="default")  {
 return \o\Runtime::concat($u_arg, "!");
 return \o\Runtime::void(__METHOD__);
}
\o\v($u_t)->u_ok((u_test_b() === "default!"), "default");
function u_test_sum ()  {
 $u_asum = 0;
foreach (\o\uv(\o\Runtime::getModule(__NAMESPACE__, 'Meta')->u_arguments()) as $u_arg) {
$u_asum += \o\vn($u_arg, 1);

}
return $u_asum;
 return \o\Runtime::void(__METHOD__);
}
$u_sum = u_test_sum(1, 2, 3, 4);
\o\v($u_t)->u_ok(($u_sum === 10), "variable args");
function u_with_op ($u_foo, $u_bar="default")  {
 return $u_bar;
 return \o\Runtime::void(__METHOD__);
}
$u_r = u_with_op("hello", "world");
\o\v($u_t)->u_ok(($u_r === "world"), "default, supplied");
$u_r = u_with_op("hello");
\o\v($u_t)->u_ok(($u_r === "default"), "default, fallback");
$u_outer = "OUT";
$u_fun_closure = function  ($u_a) use ($u_outer) {
 return \o\Runtime::concat(\o\Runtime::concat($u_a, "/"), $u_outer);
 return \o\Runtime::void(__METHOD__);
}
;
\o\v($u_t)->u_ok(($u_fun_closure("IN") === "IN/OUT"), "closure");
function u_add_to_list ($u_l)  {
 $u_l []= 4;
 return \o\Runtime::void(__METHOD__);
}
$u_ref_list = \o\OList::create([ 1, 2, 3 ]);
u_add_to_list($u_ref_list);
\o\v($u_t)->u_ok((\o\v($u_ref_list)->u_length() === 4), "list (object) - pass by ref - changed");
\o\v($u_ref_list)->u_reverse();
\o\v($u_t)->u_ok((\o\v($u_ref_list)[0] === 1), "list.reverse - not changed in place");
function u_add_to_string ($u_s)  {
 $u_s .= "4";
 return \o\Runtime::void(__METHOD__);
}
$u_ref_str = "123";
u_add_to_string($u_ref_str);
\o\v($u_t)->u_ok((\o\v($u_ref_str)->u_length() === 3), "string - pass by ref - unchanged");
$u_fn_no_return = function  ()  {
 $u_v = u_no_return();
\o\v($u_v)->u_reverse();
 return \o\Runtime::void(__METHOD__);
}
;
\o\v($u_t)->u_dies($u_fn_no_return, "returned Nothing");
function u_missing_args ($u_arg1, $u_arg2)  {
  return \o\Runtime::void(__METHOD__);
}
\o\v($u_t)->u_dies(function  ()  {
 u_missing_args(1);
 return \o\Runtime::void(__METHOD__);
}
, "Missing argument - user function");
\o\v($u_t)->u_dies(function  ()  {
 \o\Runtime::getModule(__NAMESPACE__, 'File')->u_read();
 return \o\Runtime::void(__METHOD__);
}
, "Missing argument - module");
\o\v($u_t)->u_section("Function - Argument Checking");
\o\v($u_t)->u_ok(\o\v($u_t)->u_check_args_string(""), "string");
\o\v($u_t)->u_ok(\o\v($u_t)->u_check_args_number(123), "number");
\o\v($u_t)->u_ok(\o\v($u_t)->u_check_args_list(\o\OList::create([  ])), "list");
\o\v($u_t)->u_ok(\o\v($u_t)->u_check_args_flag(false), "flag");
\o\v($u_t)->u_ok(\o\v($u_t)->u_check_args_map(\o\OMap::create([  ])), "map");
\o\v($u_t)->u_ok(\o\v($u_t)->u_check_args_multi("", 0, \o\OList::create([  ])), "multi: string, number, list");
\o\v($u_t)->u_dies(function  ()  {
 \o\v($u_t)->u_check_args_map(true, true);
 return \o\Runtime::void(__METHOD__);
}
, "Too many args");
\o\v($u_t)->u_dies(function  ()  {
 \o\v($u_t)->u_check_args_map(\o\OList::create([  ]));
 return \o\Runtime::void(__METHOD__);
}
, "Expect map.  Got List.");
\o\v($u_t)->u_dies(function  ()  {
 \o\v($u_t)->u_check_args_map("x");
 return \o\Runtime::void(__METHOD__);
}
, "Expect map. Got String");
\o\v($u_t)->u_dies(function  ()  {
 \o\v($u_t)->u_check_args_map(123);
 return \o\Runtime::void(__METHOD__);
}
, "Expect map. Got Number");
\o\v($u_t)->u_dies(function  ()  {
 \o\v($u_t)->u_check_args_map(true);
 return \o\Runtime::void(__METHOD__);
}
, "Expect map. Got Flag");
\o\v($u_t)->u_ok(\o\v($u_t)->u_check_args_string(123), "Number as string");
\o\v($u_t)->u_dies(function  ()  {
 \o\v($u_t)->u_check_args_number("123");
 return \o\Runtime::void(__METHOD__);
}
, "String as number");
\o\v($u_t)->u_dies(function  ()  {
 \o\v($u_t)->u_check_args_multi(true, 123, \o\OList::create([  ]));
 return \o\Runtime::void(__METHOD__);
}
, "Multi (snl): bad #1");
\o\v($u_t)->u_dies(function  ()  {
 \o\v($u_t)->u_check_args_multi("", "123", \o\OList::create([  ]));
 return \o\Runtime::void(__METHOD__);
}
, "Multi (snl): bad #2");
\o\v($u_t)->u_dies(function  ()  {
 \o\v($u_t)->u_check_args_multi("", 123, "x");
 return \o\Runtime::void(__METHOD__);
}
, "Multi (snl): bad #3");
 return \o\Runtime::void(__METHOD__);
}
function u_test_maps ($u_t)  {
 \o\v($u_t)->u_section("Maps");
$u_user = \o\OMap::create([ 'name' => "Drac", 'age' => 500, 'friends' => \o\OList::create([ \o\OMap::create([ 'name' => "Igor" ]) ]) ]);
\o\v($u_t)->u_ok((\o\v($u_user)["name"] === "Drac"), "bracket");
\o\v($u_t)->u_ok((\o\v($u_user)->u_get("name") === "Drac"), "get");
\o\v($u_t)->u_ok((\o\v($u_user)->u_length() === 3), "length");
\o\v($u_t)->u_ok((\o\v($u_user)->u_get("foo", "bar") === "bar"), "default");
\o\v($u_t)->u_ok((\o\v($u_user)->u_get(\o\OList::create([ "friends", 0, "name" ])) === "Igor"), "chained");
\o\v($u_t)->u_ok((\o\v(\o\v(\o\v($u_user)["friends"])[0])["name"] === "Igor"), "chained brackets");
\o\v($u_t)->u_ok((\o\v($u_user)->u_get(\o\OList::create([ "friends", 1, "name" ]), false) === false), "chained fail");
\o\v($u_user)["height"] = "6ft";
\o\v($u_t)->u_ok((\o\v($u_user)->u_get("height") === "6ft"), "put");
$u_map_num = \o\OMap::create([ 'name' => "Frank", 'age' => 8 ]);
\o\v($u_t)->u_ok((\o\v($u_map_num)->u_get("age") === 8), "numeric val");
$u_mlmap = \o\OMap::create([ 'name' => "Joe", 'id' => 12345 ]);
\o\v($u_t)->u_ok((\o\v($u_mlmap)->u_id === 12345), "multiline map");
\o\v($u_mlmap)["foo"] = \o\v($u_mlmap)["foo"] ?: 33;
\o\v($u_t)->u_ok((\o\v($u_mlmap)->u_foo === 33), "default assign");
\o\v($u_t)->u_section("Maps - dot access");
\o\v($u_t)->u_ok((\o\v($u_user)->u_name === "Drac"), "dot access");
\o\v($u_t)->u_dies(function  () use ($u_user) {
 \o\OBare::u_print(\o\v($u_user)->u_name_x);
 return \o\Runtime::void(__METHOD__);
}
, "dot access - missing field dies");
\o\v($u_t)->u_section("Maps - Missing values");
$u_empty = \o\OMap::create([ 'foo' => 1 ]);
\o\v($u_t)->u_ok((\o\Runtime::concat(\o\v($u_empty)["missing"], "yo") === "yo"), "empty concat");
\o\v($u_t)->u_ok((\o\v($u_empty)["missing"] === ""), "empty string");
\o\v($u_t)->u_ok(((! \o\v($u_empty)["missing"]) === true), "empty bool");
\o\v($u_empty)["def"] = \o\v($u_empty)["def"] ?: "default";
\o\v($u_t)->u_ok((\o\v($u_empty)["def"] === "default"), "empty or assign");
\o\v($u_t)->u_section("Maps - Explicit default");
$u_dmap = \o\v(\o\OMap::create([ 'foo' => 1 ]))->u_default("HAY");
\o\v($u_t)->u_ok((\o\v($u_dmap)["missing"] === "HAY"), "Map default - not found");
\o\v($u_t)->u_ok((\o\v($u_dmap)["foo"] === 1), "Map default - found");
$u_count_map = \o\v(\o\OMap::create([  ]))->u_default(0);
\o\v($u_count_map)["a"] += \o\vn(100, 1);
\o\v($u_t)->u_ok((\o\v($u_count_map)->u_a === 100), "numeric default");
\o\v($u_t)->u_section("Maps - Methods");
$u_map = \o\OMap::create([ 'a' => 1, 'b' => 2 ]);
\o\v($u_t)->u_ok(\o\v($u_map)->u_has_key("b"), "hasKey true");
\o\v($u_t)->u_ok((! \o\v($u_map)->u_has_key("z")), "hasKey false");
\o\v($u_t)->u_ok(\o\v($u_map)->u_has_value(2), "hasValue true");
\o\v($u_t)->u_ok((! \o\v($u_map)->u_has_value(99)), "hasValue false");
\o\v($u_t)->u_ok((\o\v(\o\v($u_map)->u_keys())->u_join("|") === "a|b"), "keys");
\o\v($u_t)->u_ok((\o\v(\o\v($u_map)->u_values())->u_join("|") === "1|2"), "values");
$u_map2 = \o\v($u_map)->u_copy();
\o\v($u_map2)["b"] = 3;
\o\v($u_t)->u_ok((\o\v($u_map)->u_b === 2), "copy");
\o\v($u_t)->u_ok((\o\v($u_map2)->u_b === 3), "copy");
\o\v($u_t)->u_ok((! \o\v($u_map2)->u_is_empty()), "not isEmpty");
\o\v($u_map2)->u_clear();
\o\v($u_t)->u_ok(\o\v($u_map2)->u_is_empty(), "clear / isEmpty");
\o\v($u_t)->u_ok((\o\v($u_map)->u_remove("b") === 2), "delete - key exists");
\o\v($u_t)->u_dies(function  () use ($u_map) {
 \o\v($u_map)->u_remove("Z");
 return \o\Runtime::void(__METHOD__);
}
, "delete - key nonexistent");
\o\v($u_t)->u_ok((\o\v(\o\v($u_map)->u_keys())->u_length() === 1), "delete - modified map");
$u_map = \o\OMap::create([ 'a' => 1, 'b' => 2, 'c' => 1 ]);
$u_flipped = \o\v($u_map)->u_reverse();
\o\v($u_t)->u_ok((\o\v($u_flipped)["1"] === "c"), "reverse");
\o\v($u_t)->u_ok((\o\v($u_flipped)["2"] === "b"), "reverse");
\o\v($u_t)->u_ok((\o\v($u_flipped)->u_length() === 2), "reverse length");
\o\v($u_t)->u_section("Maps - Size Errors");
\o\v($u_t)->u_dies(function  ()  {
 \o\v(\o\OMap::create([  ]))->u_remove("Z");
 return \o\Runtime::void(__METHOD__);
}
, "Map key not found");
\o\v($u_t)->u_dies(function  ()  {
 \o\v(\o\OMap::create([  ]))->u_get_key("VAL");
 return \o\Runtime::void(__METHOD__);
}
, "Map value not found");
 return \o\Runtime::void(__METHOD__);
}
function u_test_math_and_logic ($u_t)  {
 \o\v($u_t)->u_section("Math operators");
$u_a = 2;
$u_b = (\o\vn((\o\vn($u_a, 1) + \o\vn(1, 1)), 1) + \o\vn(2, 1));
$u_c = (\o\vn((\o\vn($u_a, 0) * \o\vn(3, 0)), 1) + \o\vn(1, 1));
$u_d = (\o\vn($u_a, 0) / \o\vn(2, 0));
$u_e = (\o\vn(5, 0) % \o\vn(2, 0));
$u_f = (\o\vn(3, 1) + \o\vn((- 1), 1));
$u_g = (\o\vn((- 1), 1) + \o\vn((- 1), 1));
$u_h = (\o\vn(2, 0) ** \o\vn(4, 0));
\o\v($u_t)->u_ok(($u_a === 2), "assign");
\o\v($u_t)->u_ok(($u_b === 5), "add");
\o\v($u_t)->u_ok(($u_c === 7), "mult, add");
\o\v($u_t)->u_ok(($u_d === 1), "divide");
\o\v($u_t)->u_ok(($u_e === 1), "modulo");
\o\v($u_t)->u_ok(($u_f === 2), "plus negative");
\o\v($u_t)->u_ok(($u_h === 16), "exponent");
$u_fp = (\o\vn(1.1, 1) + \o\vn(2.2, 1));
\o\v($u_t)->u_ok(((\o\vn($u_fp, 0) > \o\vn(3.2, 0)) && (\o\vn($u_fp, 0) < \o\vn(3.4, 0))), "floating point");
\o\v($u_t)->u_ok(((\o\vn(1000000, 1) + \o\vn(2000, 1)) === 1002000), "_ separator");
\o\v($u_t)->u_section("Strict Math");
\o\v($u_t)->u_dies(function  ()  {
 return (\o\vn("a", 1) + \o\vn(2, 1));
 return \o\Runtime::void(__METHOD__);
}
, "Add string to number");
\o\v($u_t)->u_dies(function  ()  {
 return (\o\vn(2, 1) + \o\vn("b", 1));
 return \o\Runtime::void(__METHOD__);
}
, "Add number to string");
\o\v($u_t)->u_dies(function  ()  {
 return (\o\vn("a", 0) * \o\vn(2, 0));
 return \o\Runtime::void(__METHOD__);
}
, "Multiply string");
\o\v($u_t)->u_dies(function  ()  {
 return (\o\vn("a", 0) % \o\vn(2, 0));
 return \o\Runtime::void(__METHOD__);
}
, "Modulo string");
\o\v($u_t)->u_dies(function  ()  {
 return (\o\vn(true, 1) + \o\vn(2, 1));
 return \o\Runtime::void(__METHOD__);
}
, "Add flag to number");
\o\v($u_t)->u_dies(function  ()  {
 return (\o\vn(\o\OMap::create([  ]), 1) + \o\vn(2, 1));
 return \o\Runtime::void(__METHOD__);
}
, "Add Map to number");
\o\v($u_t)->u_dies(function  ()  {
 $u_aa = 1;
$u_aa += \o\vn("v", 1);
 return \o\Runtime::void(__METHOD__);
}
, "+= string");
\o\v($u_t)->u_dies(function  ()  {
 return (\o\vn(1, 0) > \o\vn("a", 0));
 return \o\Runtime::void(__METHOD__);
}
, "number > string");
\o\v($u_t)->u_dies(function  ()  {
 return (\o\vn(1, 0) >= \o\vn("a", 0));
 return \o\Runtime::void(__METHOD__);
}
, "number >= string");
\o\v($u_t)->u_dies(function  ()  {
 return (\o\vn(1, 0) < \o\vn("a", 0));
 return \o\Runtime::void(__METHOD__);
}
, "number < string");
\o\v($u_t)->u_dies(function  ()  {
 return (\o\vn(1, 0) <= \o\vn("a", 0));
 return \o\Runtime::void(__METHOD__);
}
, "number <= string");
\o\v($u_t)->u_dies(function  ()  {
 return (\o\vn(2, 0) ** \o\vn("a", 0));
 return \o\Runtime::void(__METHOD__);
}
, "number ** string");
\o\v($u_t)->u_dies(function  ()  {
 return (\o\vn(2, 0) / \o\vn(0, 0));
 return \o\Runtime::void(__METHOD__);
}
, "divide by zero");
\o\v($u_t)->u_section("Truth");
\o\v($u_t)->u_ok((! false), "! false");
\o\v($u_t)->u_ok(true, "true");
\o\v($u_t)->u_ok((true || false), "||");
\o\v($u_t)->u_ok((true && true), "&&");
\o\v($u_t)->u_ok((! (true && false)), "! &&");
\o\v($u_t)->u_ok((! (false || false)), "! ||");
\o\v($u_t)->u_section("Positive/Negative");
\o\v($u_t)->u_ok((\o\vn((- 1), 0) < \o\vn(1, 0)), "< negative");
\o\v($u_t)->u_ok((\o\vn(1, 0) > \o\vn((- 1), 0)), "> negative");
\o\v($u_t)->u_ok(((\o\vn(2, 0) * \o\vn((- 1), 0)) === (- 2)), "times negative");
\o\v($u_t)->u_ok(((\o\vn((+ 2), 1) + \o\vn((+ 2), 1)) === 4), "unary plus");
\o\v($u_t)->u_section("Comparison");
\o\v($u_t)->u_ok((1 === 1), "==");
\o\v($u_t)->u_ok((1 !== 2), "!=");
\o\v($u_t)->u_ok((\o\vn(1, 0) < \o\vn(2, 0)), "<");
\o\v($u_t)->u_ok((\o\vn(2, 0) > \o\vn(1, 0)), ">");
\o\v($u_t)->u_ok((\o\vn(4, 0) >= \o\vn(3, 0)), ">= gt");
\o\v($u_t)->u_ok((\o\vn(2, 0) <= \o\vn(3, 0)), "<= lt");
\o\v($u_t)->u_ok((\o\vn(3, 0) >= \o\vn(3, 0)), ">= eq");
\o\v($u_t)->u_ok((\o\vn(3, 0) <= \o\vn(3, 0)), "<= eq");
$u_num = 5;
\o\v($u_t)->u_ok((\o\v($u_num)->u_compare_to(10) === (- 1)), "compare num -");
\o\v($u_t)->u_ok((\o\v($u_num)->u_compare_to((- 5)) === 1), "compare num +");
\o\v($u_t)->u_ok((\o\v($u_num)->u_compare_to(5) === 0), "compare num =");
$u_str = "moo";
\o\v($u_t)->u_ok((\o\v($u_str)->u_compare_to("zoo") === (- 1)), "compare string -");
\o\v($u_t)->u_ok((\o\v($u_str)->u_compare_to("abcdef") === 1), "compare string +");
\o\v($u_t)->u_ok((\o\v($u_str)->u_compare_to("moo") === 0), "compare string =");
\o\v($u_t)->u_section("Math Assignment");
$u_aa = 10;
$u_aa += \o\vn(10, 1);
\o\v($u_t)->u_ok(($u_aa === 20), "+=");
$u_aa *= \o\vn(2, 0);
\o\v($u_t)->u_ok(($u_aa === 40), "*=");
$u_aa -= \o\vn(30, 0);
\o\v($u_t)->u_ok(($u_aa === 10), "-=");
$u_aa /= \o\vn(2, 0);
\o\v($u_t)->u_ok(($u_aa === 5), "/=");
$u_aa **= \o\vn(2, 0);
\o\v($u_t)->u_ok(($u_aa === 25), "**=");
\o\v($u_t)->u_section("Number Methods");
$u_num = 1234.56;
\o\v($u_t)->u_ok((\o\v($u_num)->u_format() === "1,235"), "format");
\o\v($u_t)->u_ok((\o\v($u_num)->u_format(1) === "1,234.6"), "format - numDecimals");
\o\v($u_t)->u_ok((\o\v($u_num)->u_format(2, "") === "1234.56"), "format - blank sep");
\o\v($u_t)->u_ok((\o\v($u_num)->u_format(2, " ", ",") === "1 234,56"), "format - sep & dec");
\o\v($u_t)->u_ok((\o\v($u_num)->u_to_string() === "1234.56"), "toString");
\o\v($u_t)->u_ok((\o\v($u_num)->u_to_flag() === true), "toFlag");
\o\v($u_t)->u_ok((\o\v(0)->u_to_flag() === false), "toFlag - false");
\o\v($u_t)->u_ok((\o\v((- 1))->u_to_flag() === true), "toFlag - negative");
\o\v($u_t)->u_ok((\o\v(0.1)->u_to_flag() === true), "toFlag - float");
 return \o\Runtime::void(__METHOD__);
}
function u_test_control_flow ($u_t)  {
 \o\v($u_t)->u_section("Loops");
$u_s = "";
foreach (\o\uv(\o\OBare::u_range(1, 3)) as $u_i) {
$u_s .= $u_i;

}
\o\v($u_t)->u_ok(($u_s === "123"), "for, range");
$u_nums = \o\OList::create([ 4, 5, 6 ]);
foreach (\o\uv($u_nums) as $u_n) {
$u_s .= $u_n;

}
\o\v($u_t)->u_ok(($u_s === "123456"), "for, list");
$u_pairs = \o\OMap::create([ 'a' => 1, 'b' => 2, 'c' => 3 ]);
$u_s = "";
foreach (\o\uv($u_pairs) as $u_letter => $u_number) {
$u_s .= \o\Runtime::concat($u_number, $u_letter);

}
\o\v($u_t)->u_ok(($u_s === "1a2b3c"), "for, map");
$u_i = 0;
$u_s = "";
while (true) {
$u_i += \o\vn(1, 1);
$u_s .= $u_i;
if (($u_i === 3)) {
break;

}


}
\o\v($u_t)->u_ok(($u_s === "123"), "break");
$u_i = 0;
$u_s = "";
while (true) {
$u_i += \o\vn(1, 1);
if (($u_i === 4)) {
continue;

}

$u_s .= $u_i;
if (($u_i === 5)) {
break;

}


}
\o\v($u_t)->u_ok(($u_s === "1235"), "continue");
\o\v($u_t)->u_parser_error("for {\nlet a = 1;\n}\n", "needs a 'break'");
\o\v($u_t)->u_parser_error("for {\nlet a = 1;\nreturn;\n}\n", "needs a 'break'");
\o\v($u_t)->u_section("Logic Assignment");
$u_a = (0 ?: 5);
\o\v($u_t)->u_ok(($u_a === 5), "||: false");
$u_a = (2 ?: 5);
\o\v($u_t)->u_ok(($u_a === 2), "||: true");
$u_a = (\o\Runtime::andPush(0) ? 5 : \o\Runtime::andPop());
\o\v($u_t)->u_ok(($u_a === 0), "&&: false");
$u_a = (\o\Runtime::andPush(2) ? 5 : \o\Runtime::andPop());
\o\v($u_t)->u_ok(($u_a === 5), "&&: true");
$u_a = (0 ?: (\o\Runtime::andPush(2) ? 4 : \o\Runtime::andPop()));
\o\v($u_t)->u_ok(($u_a === 4), "||: &&:");
$u_a = (\o\Runtime::andPush(1) ? (0 ?: 5) : \o\Runtime::andPop());
\o\v($u_t)->u_ok(($u_a === 5), "&&: ||:");
$u_a = (0 ?: ("" ?: 6));
\o\v($u_t)->u_ok(($u_a === 6), "||: ||:");
$u_a = (\o\Runtime::andPush(1) ? (\o\Runtime::andPush(2) ? 3 : \o\Runtime::andPop()) : \o\Runtime::andPop());
\o\v($u_t)->u_ok(($u_a === 3), "&&: &&:");
$u_a = 1;
$u_a = $u_a ? 5 : $u_a;
\o\v($u_t)->u_ok(($u_a === 5), "&&= true");
$u_a = 0;
$u_a = $u_a ? 3 : $u_a;
\o\v($u_t)->u_ok(($u_a === 0), "&&= false");
$u_a = 0;
$u_a = $u_a ?: 2;
\o\v($u_t)->u_ok(($u_a === 2), "||= true");
$u_a = $u_a ?: 3;
\o\v($u_t)->u_ok(($u_a === 2), "||= false");
\o\v($u_t)->u_section("if/else");
$u_a = 1;
if (true) {
$u_a = 2;

}

\o\v($u_t)->u_ok(($u_a === 2), "if true");
if (false) {
$u_a = 3;

}

\o\v($u_t)->u_ok(($u_a === 2), "if false");
if (false) {
$u_a = 3;

}
 else {
$u_a = 4;

}

\o\v($u_t)->u_ok(($u_a === 4), "else");
if (false) {
$u_a = 3;

}
 else if (true) {
$u_a = 5;

}


\o\v($u_t)->u_ok(($u_a === 5), "else if");
if (false) {
$u_a = 3;

}
 else if (false) {
$u_a = 5;

}
 else if (false) {
$u_a = 9;

}
 else {
$u_a = 6;

}



\o\v($u_t)->u_ok(($u_a === 6), "if, else if, else");
\o\v($u_t)->u_section("Misc");
$u_ex = false;
$u_fin = false;
try {
\o\OBare::u_die("ERROR!");

}
 catch (\Exception $u_err) {
$u_ex = $u_err;

}
 finally {
$u_fin = true;

}

\o\v($u_t)->u_ok((\o\v($u_err)->u_message() === "ERROR!"), "try/catch thrown");
\o\v($u_t)->u_ok($u_fin, "try/catch - finally");
$u_file_ex = false;
try {
\o\Runtime::getModule(__NAMESPACE__, 'File')->u_read("sdfsdfsdf");

}
 catch (\Exception $u_e) {
$u_file_ex = \o\v($u_e)->u_message();

}

\o\v($u_t)->u_ok(\o\v($u_file_ex)->u_contains("File does not exist"), "catch File exception");
\o\v($u_t)->u_section("Ternary");
\o\v($u_t)->u_ok(((\o\vn(2, 0) > \o\vn(1, 0)) ? true : false), "true");
\o\v($u_t)->u_ok(((\o\vn(1, 0) > \o\vn(2, 0)) ? false : true), "false");
 return \o\Runtime::void(__METHOD__);
}
function u_test_strings ($u_t)  {
 \o\v($u_t)->u_section("Strings");
$u_stra = "456789";
\o\v($u_t)->u_ok((\o\v($u_stra)[(- 1)] === "9"), "substring index");
$u_ml = "this is a
multiline
string.";
\o\v($u_t)->u_ok(\o\v($u_ml)->u_contains("multiline\nstring"), "multiline with indent");
\o\v($u_t)->u_section("String Concatenation");
\o\v($u_t)->u_ok((\o\Runtime::concat("a", "b") === "ab"), "a ~ b");
\o\v($u_t)->u_ok((\o\Runtime::concat("a", 1) === "a1"), "a ~ 1");
\o\v($u_t)->u_ok((\o\Runtime::concat(1.2, "b") === "1.2b"), "1.2 ~ b");
\o\v($u_t)->u_ok((\o\Runtime::concat(true, "!") === "true!"), "true ~ !");
\o\v($u_t)->u_ok((\o\Runtime::concat(false, "!") === "false!"), "false ~ !");
$u_s = "a";
$u_s .= "bc";
\o\v($u_t)->u_ok(($u_s === "abc"), "~=");
\o\v($u_t)->u_section("String Methods");
$u_hi = "Hello World!";
\o\v($u_t)->u_ok((\o\v("abcdef")->u_reverse() === "fedcba"), "direct string method");
\o\v($u_t)->u_ok((\o\v($u_hi)->u_length() === 12), "length()");
\o\v($u_t)->u_ok((\o\v($u_hi)->u_char_at(1) === "e"), "get()");
\o\v($u_t)->u_ok((\o\v($u_hi)->u_char_at((- 1)) === "!"), "get() negative");
\o\v($u_t)->u_ok(\o\v($u_hi)->u_contains("Hello"), "has()");
\o\v($u_t)->u_ok((! \o\v($u_hi)->u_contains("missing")), "! has()");
\o\v($u_t)->u_ok((\o\v(\o\v($u_hi)->u_split("o"))->u_length() === 3), "split()");
\o\v($u_t)->u_ok((\o\v(\o\v($u_hi)->u_split("o"))[0] === "Hell"), "split()");
\o\v($u_t)->u_ok((\o\Runtime::concat(\o\Runtime::getModule(__NAMESPACE__, 'String')->u_char_from_code(65), \o\Runtime::getModule(__NAMESPACE__, 'String')->u_char_from_code(122)) === "Az"), "String.fromCharCode");
\o\v($u_t)->u_ok((\o\v("false")->u_to_flag() === false), "toFlag - false");
\o\v($u_t)->u_ok((\o\v("true")->u_to_flag() === true), "toFlag - true");
\o\v($u_t)->u_ok((\o\v("0")->u_to_flag() === false), "toFlag - 0");
\o\v($u_t)->u_ok((\o\v("null")->u_to_flag() === false), "toFlag - null");
\o\v($u_t)->u_ok((\o\v("123")->u_to_number() === 123), "toNumber");
\o\v($u_t)->u_ok((\o\v("99ft")->u_to_number() === 99), "toNumber - trailing letters");
\o\v($u_t)->u_section("String Methods - Unicode");
$u_uni = "ⒶⒷⒸ①②③ abc123";
\o\v($u_t)->u_ok((\o\v($u_uni)->u_length() === 13), "length");
\o\v($u_t)->u_ok((\o\v($u_uni)->u_char_at(2) === "Ⓒ"), "charAt");
\o\v($u_t)->u_ok((\o\v($u_uni)->u_char_at((- 1)) === "3"), "charAt negative");
\o\v($u_t)->u_ok((\o\v($u_uni)->u_char_code_at(2) === 9400), "codeAt");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'String')->u_char_from_code(9400) === "Ⓒ"), "charFromCode");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'String')->u_char_from_code(65) === "A"), "charFromCode, ascii");
\o\v($u_t)->u_ok((\o\v($u_uni)->u_left(3) === "ⒶⒷⒸ"), "left");
\o\v($u_t)->u_ok((\o\v($u_uni)->u_right(3) === "123"), "right");
\o\v($u_t)->u_ok((\o\v($u_uni)->u_substring(4, 5) === "②③ ab"), "substring");
\o\v($u_t)->u_ok((\o\v($u_uni)->u_substring(3) === "①②③ abc123"), "substring - remainder");
\o\v($u_t)->u_ok(\o\v($u_uni)->u_starts_with("ⒶⒷⒸ"), "startsWith");
\o\v($u_t)->u_ok(\o\v("ab ⒶⒷ")->u_ends_with("ⒶⒷ"), "endsWith");
\o\v($u_t)->u_ok(\o\v("abc ⒶⒷ")->u_starts_with("AbC", true), "startsWith ignoreCase");
\o\v($u_t)->u_ok(\o\v($u_uni)->u_ends_with("ABc123", true), "endsWith ignoreCase");
\o\v($u_t)->u_ok((\o\v(" ⒶⒷ ⒶⒷ")->u_index_of("ⒶⒷ") === 1), "indexOf");
\o\v($u_t)->u_ok((\o\v(" ⒶⒷ ⒶⒷ")->u_index_of("ⒶⒷ", 2) === 4), "indexOf - offset");
\o\v($u_t)->u_ok((\o\v("abc")->u_index_of("BC", 0, true) === 1), "indexOf - ignoreCase");
\o\v($u_t)->u_ok((\o\v(" ⒶⒷ ⒶⒷ")->u_last_index_of("ⒶⒷ") === 4), "lastIndexOf");
\o\v($u_t)->u_ok((\o\v(" ⒶⒷ ⒶⒷ")->u_last_index_of("ⒶⒷ", 3) === 1), "lastIndexOf - offset");
\o\v($u_t)->u_ok((\o\v("abab")->u_last_index_of("AB", 0, true) === 2), "lastIndexOf - ignoreCase");
\o\v($u_t)->u_ok((\o\v("ⒶⒸ")->u_insert("Ⓑ", 1) === "ⒶⒷⒸ"), "insert");
\o\v($u_t)->u_ok((\o\v("ⒶⒷⒸ")->u_insert("①", (- 2)) === "Ⓐ①ⒷⒸ"), "insert negative index");
\o\v($u_t)->u_ok(\o\v($u_uni)->u_contains("③ a"), "contains");
\o\v($u_t)->u_ok(\o\v($u_uni)->u_contains("③ ABc", true), "contains ignoreCase");
\o\v($u_t)->u_ok((\o\v("aⒷⒸ")->u_pad_left(5, " ") === "  aⒷⒸ"), "pad left");
\o\v($u_t)->u_ok((\o\v("aⒷⒸ")->u_pad_left(5) === "  aⒷⒸ"), "pad left - no char");
\o\v($u_t)->u_ok((\o\v("aⒷⒸ")->u_pad_right(5, "①") === "aⒷⒸ①①"), "pad right char");
\o\v($u_t)->u_ok((\o\v("aⒷⒸ")->u_pad(5, " ") === " aⒷⒸ "), "pad both");
\o\v($u_t)->u_ok((\o\v("aⒷⒸ")->u_pad(6, " ") === " aⒷⒸ  "), "pad both uneven");
\o\v($u_t)->u_ok((\o\v("  ⒶⒷ ①②  ")->u_trim() === "ⒶⒷ ①②"), "trim");
\o\v($u_t)->u_ok((\o\v("③③  ⒶⒷ ①②  ③")->u_trim("③") === "ⒶⒷ ①②"), "trim mask");
\o\v($u_t)->u_ok((\o\v("  ⒶⒷ ①②")->u_trim_left() === "ⒶⒷ ①②"), "leftTrim");
\o\v($u_t)->u_ok((\o\v("ⒶⒷ ①②  ")->u_trim_right() === "ⒶⒷ ①②"), "rightTrim");
\o\v($u_t)->u_ok((\o\v("ⒶⒷ ①②  ③")->u_trim_right("③") === "ⒶⒷ ①②"), "rightTrim mask");
\o\v($u_t)->u_ok((\o\v("③ ⒶⒷ ①②")->u_trim_left("③") === "ⒶⒷ ①②"), "leftTrim mask");
\o\v($u_t)->u_ok((\o\v("Abc DEF ⒶⒷⒸ")->u_to_upper_case() === "ABC DEF ⒶⒷⒸ"), "upperCase");
\o\v($u_t)->u_ok((\o\v("Abc DEF ⒶⒷⒸ")->u_to_lower_case() === "abc def ⒶⒷⒸ"), "lowerCase");
\o\v($u_t)->u_ok((\o\v("fòôbàř")->u_to_upper_case() === "FÒÔBÀŘ"), "upperCase - extended");
\o\v($u_t)->u_ok((\o\v("FÒÔBÀŘ")->u_to_lower_case() === "fòôbàř"), "lowerCase - extended");
\o\v($u_t)->u_ok((\o\v("ABC")->u_to_lower_case_first() === "aBC"), "lowerCaseFirst");
\o\v($u_t)->u_ok((\o\v("abc")->u_to_upper_case_first() === "Abc"), "upperCaseFirst");
\o\v($u_t)->u_ok((\o\v("ŘÔÀŘ")->u_to_lower_case_first() === "řÔÀŘ"), "lowerCaseFirst - extended");
\o\v($u_t)->u_ok((\o\v("řôàř")->u_to_upper_case_first() === "Řôàř"), "upperCaseFirst - extended");
\o\v($u_t)->u_ok((\o\v("this is a title")->u_to_title_case() === "This is a Title"), "titleCase");
\o\v($u_t)->u_ok((\o\v("a title")->u_to_title_case() === "A Title"), "titleCase - starting ignoreWord");
\o\v($u_t)->u_ok((\o\v("a:title")->u_to_title_case() === "A:title"), "titleCase - close punctuation");
\o\v($u_t)->u_ok((\o\v("horse")->u_to_plural(1) === "horse"), "plural no");
\o\v($u_t)->u_ok((\o\v("horse")->u_to_plural(2) === "horses"), "plural yes");
\o\v($u_t)->u_ok((\o\v("boss")->u_to_plural(2) === "bosses"), "plural s yes");
\o\v($u_t)->u_ok((\o\v("stimulus")->u_to_plural(3, "stimuli") === "stimuli"), "plural custom");
\o\v($u_t)->u_ok((\o\v("ⒶⒷⒸ123")->u_limit(3) === "ⒶⒷⒸ..."), "limit");
\o\v($u_t)->u_ok((\o\v("ⒶⒷⒸ123")->u_limit(3, "!") === "ⒶⒷⒸ!"), "limit");
\o\v($u_t)->u_ok((\o\v(\o\v("Ⓐ,Ⓑ,Ⓒ")->u_split(","))->u_join("|") === "Ⓐ|Ⓑ|Ⓒ"), "split/join");
\o\v($u_t)->u_ok((\o\v(\o\v("Ⓐ,Ⓑ,Ⓒ")->u_split(",", 2))->u_join("|") === "Ⓐ|Ⓑ,Ⓒ"), "split/join limit");
\o\v($u_t)->u_ok((\o\v(\o\v("Ⓐ, Ⓑ, Ⓒ")->u_split(new \o\ORegex (",\s+")))->u_join("|") === "Ⓐ|Ⓑ|Ⓒ"), "split/join regex");
\o\v($u_t)->u_ok((\o\v(\o\v("Ⓐ,Ⓑ,Ⓒ")->u_split(",", 0))->u_length() === 3), "split limit 0");
\o\v($u_t)->u_ok((\o\v(\o\v("Ⓐ,Ⓑ,Ⓒ")->u_split(",", (- 1)))->u_length() === 3), "split limit -1");
\o\v($u_t)->u_ok((\o\v(\o\v("ⒶⒷⒸ")->u_split(""))->u_length() === 3), "split on empty delimiter");
\o\v($u_t)->u_ok((\o\v(\o\v($u_uni)->u_split_chars())[2] === "Ⓒ"), "chars");
$u_uniml = "① item 1
② item 2

③ item 3";
\o\v($u_t)->u_ok((\o\v(\o\v($u_uniml)->u_split_lines())->u_length() === 3), "lines - count");
\o\v($u_t)->u_ok((\o\v(\o\v(\o\v($u_uniml)->u_split_lines())[1])->u_char_at(0) === "②"), "lines - trimmed indent");
\o\v($u_t)->u_ok((\o\v(\o\v($u_uniml)->u_split_lines(true))->u_length() === 4), "lines with whitespace");
\o\v($u_t)->u_ok((\o\v(\o\v("ⒶⒷⒸ ①②③ abc 123")->u_split_words())[1] === "①②③"), "words");
$u_words = \o\v("abc,123? ok.")->u_split_words(true);
\o\v($u_t)->u_ok((\o\v($u_words)->u_length() === 3), "words - bare");
\o\v($u_t)->u_ok((\o\v($u_words)[2] === "ok"), "words - bare");
\o\v($u_t)->u_ok((\o\v($u_uni)->u_reverse() === "321cba ③②①ⒸⒷⒶ"), "reverse");
\o\v($u_t)->u_ok((\o\v("<a&b>")->u_encode_html() === "&lt;a&amp;b&gt;"), "encodeHtml");
\o\v($u_t)->u_ok((\o\v("&lt;a&amp;b&gt;")->u_decode_html() === "<a&b>"), "decodeHtml");
$u_esc = "&#97;&#98;&#99;&#9312;&#9313;&#9314;";
\o\v($u_t)->u_ok((\o\v("abc①②③")->u_encode_html(true) === $u_esc), "encodeHtml all");
$u_enc = "a%20%E2%92%B7%2F%E2%92%B8%3Ad";
\o\v($u_t)->u_ok((\o\v("a Ⓑ/Ⓒ:d")->u_encode_url() === $u_enc), "encodeUrl");
\o\v($u_t)->u_ok((\o\v($u_enc)->u_decode_url() === "a Ⓑ/Ⓒ:d"), "decodeUrl");
\o\v($u_t)->u_ok((\o\v("ⒶⒷⒸ①②③")->u_remove_left("ⒶⒷ") === "Ⓒ①②③"), "removeLeft");
\o\v($u_t)->u_ok((\o\v("ⒶⒷⒸ①②③")->u_remove_left("①") === "ⒶⒷⒸ①②③"), "removeLeft - no");
\o\v($u_t)->u_ok((\o\v("Abcdef")->u_remove_left("abc", true) === "def"), "removeLeft - ignoreCase");
\o\v($u_t)->u_ok((\o\v("ⒶⒷⒸ①②③")->u_remove_right("②③") === "ⒶⒷⒸ①"), "removeRight");
\o\v($u_t)->u_ok((\o\v("ⒶⒷⒸ①②③")->u_remove_right("①") === "ⒶⒷⒸ①②③"), "removeRight - no");
\o\v($u_t)->u_ok((\o\v("abcDef")->u_remove_right("def", true) === "abc"), "removeLeft - ignoreCase");
\o\v($u_t)->u_ok((\o\v("Ⓐ    Ⓑ")->u_squeeze() === "Ⓐ Ⓑ"), "squeeze");
\o\v($u_t)->u_ok((\o\v("Ⓐ①①①①Ⓑ①①")->u_squeeze("①") === "Ⓐ①Ⓑ①"), "squeeze char");
\o\v($u_t)->u_ok((\o\v("ⒶⒷⒸ {var}")->u_fill(\o\OMap::create([ 'var' => "①②③" ])) === "ⒶⒷⒸ ①②③"), "fill");
\o\v($u_t)->u_ok((\o\v("abc {0}")->u_fill("123") === "abc 123"), "fill 1 arg");
\o\v($u_t)->u_ok((\o\v("abc {0} {1}")->u_fill("123", "456") === "abc 123 456"), "fill 2 arg");
\o\v($u_t)->u_ok((\o\v("abc {} {}")->u_fill(\o\OList::create([ "123", "456" ])) === "abc 123 456"), "fill blanks & array");
\o\v($u_t)->u_section("Strings - Checks");
\o\v($u_t)->u_ok(\o\v(" \n  ")->u_is_space(), "isSpace true");
\o\v($u_t)->u_ok((! \o\v("  .  ")->u_is_space()), "isSpace false");
\o\v($u_t)->u_ok((! \o\v("")->u_is_space()), "isSpace empty");
\o\v($u_t)->u_ok(\o\v("abc def")->u_has_space(), "hasSpace space");
\o\v($u_t)->u_ok(\o\v("abc\ndef")->u_has_space(), "hasSpace newline");
\o\v($u_t)->u_ok((! \o\v("abcdef")->u_has_space()), "hasSpace empty");
\o\v($u_t)->u_ok((! \o\v("abc 123")->u_is_upper_case()), "isUpperCase - none");
\o\v($u_t)->u_ok((! \o\v("aBc 123")->u_is_upper_case()), "isUpperCase - some");
\o\v($u_t)->u_ok(\o\v("ABC 123")->u_is_upper_case(), "isUpperCase - all");
\o\v($u_t)->u_ok((! \o\v("")->u_is_upper_case()), "isUpperCase - empty");
\o\v($u_t)->u_ok((! \o\v("abc 123")->u_has_upper_case()), "hasUpperCase - none");
\o\v($u_t)->u_ok(\o\v("aBc 123")->u_has_upper_case(), "hasUpperCase - some");
\o\v($u_t)->u_ok(\o\v("ABC 123")->u_has_upper_case(), "hasUpperCase - all");
\o\v($u_t)->u_ok((! \o\v("")->u_has_upper_case()), "hasUpperCase - empty");
\o\v($u_t)->u_ok((! \o\v("ABC 123")->u_is_lower_case()), "isLowerCase - none");
\o\v($u_t)->u_ok((! \o\v("AbC 123")->u_is_lower_case()), "isLowerCase - some");
\o\v($u_t)->u_ok(\o\v("abc 123")->u_is_lower_case(), "isLowerCase - all");
\o\v($u_t)->u_ok((! \o\v("")->u_is_lower_case()), "isLowerCase - empty");
\o\v($u_t)->u_ok((! \o\v("ABC 123")->u_has_lower_case()), "hasLowerCase - none");
\o\v($u_t)->u_ok(\o\v("AbC 123")->u_has_lower_case(), "hasLowerCase - some");
\o\v($u_t)->u_ok(\o\v("abc 123")->u_has_lower_case(), "hasLowerCase - all");
\o\v($u_t)->u_ok((! \o\v("")->u_has_lower_case()), "hasLowerCase - empty");
\o\v($u_t)->u_ok((\o\v("a b c")->u_to_token_case() === "a-b-c"), "tokenCase");
\o\v($u_t)->u_ok((\o\v("aaBbCc")->u_to_token_case() === "aa-bb-cc"), "tokenCase - from camel");
\o\v($u_t)->u_ok((\o\v("AaBbCc")->u_to_token_case() === "aa-bb-cc"), "tokenCase - from uppercamel");
\o\v($u_t)->u_ok((\o\v("AA BB CC")->u_to_token_case() === "aa-bb-cc"), "tokenCase - from uppercamel");
\o\v($u_t)->u_ok((\o\v("a b c")->u_to_token_case("__") === "a__b__c"), "tokenCase - delimiter");
\o\v($u_t)->u_ok((\o\v("aa bb cc")->u_to_camel_case() === "aaBbCc"), "camelCase");
\o\v($u_t)->u_ok((\o\v("-aa-bb--cc!")->u_to_camel_case() === "aaBbCc"), "camelCase - delim");
\o\v($u_t)->u_ok((\o\v("aa-bb-cc")->u_to_camel_case(true) === "AaBbCc"), "upperCamelCase");
\o\v($u_t)->u_ok((\o\v("a b c")->u_to_camel_case() === "aBC"), "camelCase - single chars");
\o\v($u_t)->u_ok(\o\v("abc")->u_is_alpha(), "isAlpha");
\o\v($u_t)->u_ok(\o\v("abcDEF")->u_is_alpha(), "isAlpha");
\o\v($u_t)->u_ok((! \o\v("abc123")->u_is_alpha()), "isAlpha - w numbers");
\o\v($u_t)->u_ok((! \o\v("abc def")->u_is_alpha()), "isAlpha - spaces");
\o\v($u_t)->u_ok((! \o\v("")->u_is_alpha()), "isAlpha - empty");
\o\v($u_t)->u_ok(\o\v("abc")->u_is_alpha_numeric(), "isAlphaNumeric");
\o\v($u_t)->u_ok(\o\v("abcDEF")->u_is_alpha_numeric(), "isAlphaNumeric");
\o\v($u_t)->u_ok(\o\v("abc123")->u_is_alpha_numeric(), "isAlphaNumeric - w numbers");
\o\v($u_t)->u_ok((! \o\v("abc 123")->u_is_alpha_numeric()), "isAlphaNumeric - spaces");
\o\v($u_t)->u_ok((! \o\v("")->u_is_alpha_numeric()), "isAlphaNumeric - empty");
\o\v($u_t)->u_ok(\o\v("123")->u_is_number(), "isNumber");
\o\v($u_t)->u_ok(\o\v("-123")->u_is_number(), "isNumber - negative");
\o\v($u_t)->u_ok(\o\v("123.45")->u_is_number(), "isNumber - float");
\o\v($u_t)->u_ok((! \o\v("123 ")->u_is_number()), "isNumber - space");
\o\v($u_t)->u_ok((! \o\v("123a")->u_is_number()), "isNumber - alphanum");
\o\v($u_t)->u_ok((! \o\v("abc")->u_is_number()), "isNumber - all alpha");
\o\v($u_t)->u_ok((! \o\v("")->u_is_number()), "isNumber - empty");
\o\v($u_t)->u_ok(\o\v("abc 123")->u_is_ascii(), "isAscii");
\o\v($u_t)->u_ok(\o\v("")->u_is_ascii(), "isAscii - empty");
\o\v($u_t)->u_ok((! \o\v("ⒶⒷⒸ")->u_is_ascii()), "isAscii - unicode");
\o\v($u_t)->u_ok((! \o\v("abⒸ")->u_is_ascii()), "isAscii - mixed");
\o\v($u_t)->u_section("Strings - Escapes");
\o\v($u_t)->u_ok(("abcd" === "abcd"), "string - escape normal char");
\o\v($u_t)->u_ok(\o\v("ab\ncd")->u_match(new \o\ORegex ("ab\scd")), "string - newline");
$u_esc = "\$_SERVER[\"REMOTE_ADDR\"]";
\o\v($u_t)->u_ok((! \o\v("lot's\t {} \"double \$quote\"")->u_contains("\\")), "no leaked backslashes");
\o\v($u_t)->u_ok(\o\v("Here's an escaped quote")->u_contains("'"), "escaped quote (\\')");
\o\v($u_t)->u_ok(\o\v($u_esc)->u_starts_with("\$_SERVER"), "prevent php vars - \$_SERVER");
\o\v($u_t)->u_ok((\o\v("\$abc")[0] === "\$"), "prevent php vars - \\\$abc");
\o\v($u_t)->u_ok((\o\v("\${abc}")[0] === "\$"), "prevent php vars - \${abc}");
\o\v($u_t)->u_section("Regular Expressions");
\o\v($u_t)->u_ok((\o\v(\o\v($u_hi)->u_split(new \o\ORegex ("\s")))[1] === "World!"), "split regex");
\o\v($u_t)->u_ok((\o\v(\o\v($u_hi)->u_match(new \o\ORegex ("(\w+)!\$")))[1] === "World"), "regex with dollar");
\o\v($u_t)->u_dies(function  ()  {
 \o\v("longstringlongstring")->u_find(new \o\ORegex ("(?:\D+|<\d+>)*[!?]"));
 return \o\Runtime::void(__METHOD__);
}
, "regex error");
$u_multi = "one\ntwo\nthree";
\o\v($u_t)->u_ok((\o\v(\o\v($u_multi)->u_split(new \o\ORegex ("\s")))->u_length() === 3), "Newline regex");
$u_cased = "hello WORLD";
\o\v($u_t)->u_ok((\o\v(\o\v($u_cased)->u_match(\o\v(new \o\ORegex ("world"))->u_flags("i")))[0] === "WORLD"), "regex object");
$u_ticks = "hello 'WORLD'";
\o\v($u_t)->u_ok((\o\v(\o\v($u_ticks)->u_match(new \o\ORegex ("'(\w+)'")))[1] === "WORLD"), "regex with backticks");
$u_esc_ticks = "hello `WORLD`";
\o\v($u_t)->u_ok((\o\v($u_esc_ticks)->u_replace(new \o\ORegex ("\`(\w+)\`"), "THERE") === "hello THERE"), "escaped backticks");
\o\v($u_t)->u_ok((\o\v("ab  cd e")->u_replace(new \o\ORegex ("\s+"), "-") === "ab-cd-e"), "replace");
$u_rx = \o\Runtime::getModule(__NAMESPACE__, 'Regex')->u_new(\o\v("'{0}'")->u_fill("world"), "i");
\o\v($u_t)->u_ok((\o\v($u_ticks)->u_replace($u_rx, "VAR") === "hello VAR"), "replace with variable");
\o\v($u_t)->u_section("LockStrings");
\o\v($u_t)->u_ok(\o\v(new \o\OLockString ("abc"))->u_is_lock_string(), "isLockString = true");
\o\v($u_t)->u_ok((! \o\v("abc")->u_is_lock_string()), "isLockString = false");
\o\v($u_t)->u_dies(function  ()  {
 return \o\Runtime::concat(new \o\OLockString ("a"), "b");
 return \o\Runtime::void(__METHOD__);
}
, "Can't combine");
\o\v($u_t)->u_dies(function  ()  {
 return \o\Runtime::concat("a", new \o\OLockString ("b"));
 return \o\Runtime::void(__METHOD__);
}
, "Can't combine");
$u_lock1 = new \o\OLockString ("1={},");
$u_lock2 = new \o\OLockString ("2={}");
$u_combined = \o\Runtime::concat($u_lock1, $u_lock2);
\o\v($u_combined)->u_fill(\o\OList::create([ "a", "b" ]));
\o\v($u_t)->u_ok((\o\v($u_combined)->u_unlocked() === "1=a,2=b"), "combined lockstrings");
\o\v($u_t)->u_ok((\o\v(u_lock_html("a"))->u_get_string_type() === "html"), "getStringType");
\o\v($u_t)->u_ok((\o\v(new \o\OLockString ("x"))->u_get_string_type() === "text"), "getStringType");
 return \o\Runtime::void(__METHOD__);
}
function u_test_lists ($u_t)  {
 \o\v($u_t)->u_section("Lists");
$u_ary = \o\OList::create([ 1, 2, 3, 4, 5 ]);
\o\v($u_t)->u_ok((\o\v(\o\v(\o\OList::create([ 4, 5, 6 ]))->u_reverse())[2] === 4), "direct list method");
\o\v($u_t)->u_ok((\o\v($u_ary)->u_length() === 5), "size");
\o\v($u_t)->u_ok((\o\v($u_ary)->u_get(2) === 3), "at");
\o\v($u_t)->u_ok((\o\v($u_ary)->u_get(10, 9) === 9), "default");
\o\v($u_t)->u_ok((\o\v($u_ary)[1] === 2), "direct");
\o\v($u_t)->u_ok((\o\v($u_ary)->u_join(":") === "1:2:3:4:5"), "join");
\o\v($u_t)->u_ok((\o\v(\o\v($u_ary)->u_reverse())->u_join(":") === "5:4:3:2:1"), "reverse");
$u_ary_extra_comma = \o\OList::create([ 6, 7, 8, 9 ]);
\o\v($u_t)->u_ok((\o\v($u_ary_extra_comma)->u_join(":") === "6:7:8:9"), "trailing comma");
\o\v($u_t)->u_ok((\o\v($u_ary)[(- 2)] === 4), "negative index");
\o\v($u_ary)[0] = 99;
\o\v($u_t)->u_ok((\o\v($u_ary)[0] === 99), "direct set");
$u_mlary = \o\OList::create([ "hello", \o\OMap::create([ 'name' => "world" ]), "yay" ]);
\o\v($u_t)->u_ok((\o\v(\o\v($u_mlary)[1])["name"] === "world"), "multiline array");
$u_copy_ary_a = \o\OList::create([ 1, 2 ]);
$u_copy_ary_b = \o\v($u_copy_ary_a)->u_copy();
\o\v($u_copy_ary_a)[0] = 11;
\o\v($u_copy_ary_b)[0] = 22;
\o\v($u_t)->u_ok(((\o\v($u_copy_ary_a)[0] === 11) && (\o\v($u_copy_ary_b)[0] === 22)), "copy");
$u_ary = \o\OList::create([ 1, 2, 3 ]);
\o\v($u_t)->u_ok((\o\v(\o\v($u_ary)->u_add(40))[3] === 40), "add");
\o\v($u_t)->u_ok((\o\v($u_ary)->u_remove() === 40), "remove");
\o\v($u_t)->u_ok((\o\v(\o\v($u_ary)->u_add((- 10), 0))[0] === (- 10)), "add index 0");
\o\v($u_t)->u_ok((\o\v($u_ary)->u_remove(0) === (- 10)), "remove index 0");
$u_ary = \o\OList::create([ 1, 2, 3 ]);
\o\v($u_t)->u_ok((\o\v(\o\v($u_ary)->u_add(40, (- 1)))[3] === 40), "add index -1");
\o\v($u_ary)->u_remove();
\o\v($u_t)->u_ok((\o\v(\o\v($u_ary)->u_add(40, (- 2)))[2] === 40), "add index -2");
\o\v($u_t)->u_ok((\o\v(\o\OList::create([ 0, 1, 2 ]))->u_remove((- 1)) === 2), "remove index -1");
\o\v($u_t)->u_ok((\o\v(\o\OList::create([ 0, 1, 2 ]))->u_remove((- 2)) === 1), "remove index -2");
$u_ary = \o\OList::create([ 1, 2, 3 ]);
\o\v($u_ary)->u_remove();
\o\v($u_t)->u_ok((\o\v($u_ary)->u_length() === 2), "length after remove");
\o\v($u_t)->u_ok((\o\v(\o\v(\o\OList::create([ 0, 1, 2, 3 ]))->u_sublist(1))->u_join("|") === "1|2|3"), "sublist");
\o\v($u_t)->u_ok((\o\v(\o\v(\o\OList::create([ 0, 1, 2, 3 ]))->u_sublist((- 2)))->u_join("|") === "2|3"), "sublist -2");
\o\v($u_t)->u_ok((\o\v(\o\v(\o\OList::create([ 0, 1, 2, 3 ]))->u_sublist(1, 2))->u_join("|") === "1|2"), "sublist w length");
\o\v($u_t)->u_ok((\o\v(\o\OList::create([ "aa", "bb", "'cc'" ]))[1] === "bb"), "quoted list");
\o\v($u_t)->u_ok((\o\v(\o\OList::create([ "aa", "bb", "'cc'" ]))[2] === "'cc'"), "quoted list + quotes");
$u_ml = \o\OList::create([ "aa", "bb", "'cc'" ]);
\o\v($u_t)->u_ok((\o\v($u_ml)[1] === "bb"), "multiline quoted list");
\o\v($u_t)->u_ok((\o\v($u_ml)[2] === "'cc'"), "multiline quoted list + quotes");
\o\v($u_t)->u_section("Lists - Sorting");
\o\v($u_t)->u_ok((\o\v(\o\v(\o\OList::create([ "a", "b", "c" ]))->u_sort())->u_join("|") === "a|b|c"), "sort");
\o\v($u_t)->u_ok((\o\v(\o\v(\o\OList::create([ "1", "2", "10" ]))->u_sort())->u_join("|") === "1|2|10"), "sort numeric strings");
$u_list = \o\v(\o\OList::create([ "a", "b", "c" ]))->u_sort(function  ($u_a, $u_b)  {
 return \o\v($u_b)->u_compare_to($u_a);
 return \o\Runtime::void(__METHOD__);
}
);
\o\v($u_t)->u_ok((\o\v($u_list)->u_join("|") === "c|b|a"), "sort function");
$u_list = \o\v(\o\OList::create([ 1, 3, 2 ]))->u_sort(\o\OMap::create([ 'reverse' => true ]));
\o\v($u_t)->u_ok((\o\v($u_list)->u_join("|") === "3|2|1"), "reverse sort");
$u_list = \o\v(\o\OList::create([ 1, 3, 2 ]))->u_sort(\o\OMap::create([ 'reverse' => false ]));
\o\v($u_t)->u_ok((\o\v($u_list)->u_join("|") === "1|2|3"), "non-reverse sort");
$u_list = \o\v(\o\OList::create([ "a1", "a10", "a2" ]))->u_sort(\o\OMap::create([ 'type' => "natural" ]));
\o\v($u_t)->u_ok((\o\v($u_list)->u_join("|") === "a1|a2|a10"), "natural sort");
$u_list = \o\v(\o\OList::create([ "a1", "a10", "a2" ]))->u_sort(\o\OMap::create([ 'type' => "regular" ]));
\o\v($u_t)->u_ok((\o\v($u_list)->u_join("|") === "a1|a10|a2"), "regular sort");
\o\v($u_t)->u_dies(function  ()  {
 \o\v(\o\OList::create([ "a" ]))->u_sort(\o\OMap::create([ 'type' => "nope" ]));
 return \o\Runtime::void(__METHOD__);
}
, "unknown sort type");
$u_list = \o\v(\o\OList::create([ "a1", "A2", "a3", "A4" ]))->u_sort(\o\OMap::create([ 'type' => "stringCase" ]));
\o\v($u_t)->u_ok((\o\v($u_list)->u_join("|") === "A2|A4|a1|a3"), "case sensitive");
\o\v($u_t)->u_section("Lists - Size Errors");
\o\v($u_t)->u_dies(function  ()  {
 \o\v(\o\OList::create([ 1, 2 ]))->u_remove(3);
 return \o\Runtime::void(__METHOD__);
}
, "remove()");
\o\v($u_t)->u_dies(function  ()  {
 \o\v(\o\OList::create([  ]))->u_remove();
 return \o\Runtime::void(__METHOD__);
}
, "empty");
\o\v($u_t)->u_dies(function  ()  {
 \o\v(\o\OList::create([ 1 ]))->u_sublist(2);
 return \o\Runtime::void(__METHOD__);
}
, "sublist");
\o\v($u_t)->u_dies(function  ()  {
 \o\v(\o\OList::create([ 1 ]))->u_first(2);
 return \o\Runtime::void(__METHOD__);
}
, "last");
\o\v($u_t)->u_dies(function  ()  {
 \o\v(\o\OList::create([ 1 ]))->u_last(2);
 return \o\Runtime::void(__METHOD__);
}
, "first");
 return \o\Runtime::void(__METHOD__);
}
function u_test_templates ($u_t)  {
 \o\v($u_t)->u_section("Templates");
$u_html_users = \o\v(u_template_html(\o\OList::create([ "Frodo", "Sam", "Gandalf" ])))->u_unlocked();
\o\v($u_t)->u_ok(\o\v($u_html_users)->u_match(new \o\ORegex ("<li>Frodo.*?<li>Sam.*?<li>Gandalf")), "template - loop & variables");
$u_html_users = u_template_html(\o\OList::create([ "Frodo", "<b>Sam</b>", "Gandalf" ]));
\o\v($u_t)->u_ok(\o\v(\o\v($u_html_users)->u_unlocked())->u_contains("&lt;b&gt;Sam"), "template with html escapes");
$u_p = \o\Runtime::getModule(__NAMESPACE__, 'Web')->u_parse_html(new \o\OLockString ("<h1>> Hello
<.abc>> 123"));
$u_p = \o\v($u_p)->u_unlocked();
\o\v($u_t)->u_ok(\o\v($u_p)->u_contains("<h1>Hello</h1>"), "parse html string - double arrow");
\o\v($u_t)->u_ok(\o\v($u_p)->u_contains("<div class='abc'>123</div>"), "parse html string - dotted");
\o\v($u_t)->u_section("Template Escaping");
\o\v($u_t)->u_ok(\o\v(\o\v(u_ent_html())->u_unlocked())->u_contains("&gt;"), "html - entity");
\o\v($u_t)->u_ok(\o\v(\o\v(u_format_block_html())->u_unlocked())->u_contains("&lt;foo&gt;"), "html - format block");
$u_h = \o\v(u_exp_html("\"'", "a&b\""))->u_unlocked();
\o\v($u_t)->u_ok(\o\v($u_h)->u_contains("<p &quot;&#039;>"), "html - tag attribute");
\o\v($u_t)->u_ok(\o\v($u_h)->u_contains("a&amp;b"), "html - outer");
\o\v($u_t)->u_ok(\o\v(\o\v(u_tags_html(u_in_css()))->u_unlocked())->u_contains("<style"), "html - css style block");
\o\v($u_t)->u_ok(\o\v(\o\v(u_tags_html(u_in_js()))->u_unlocked())->u_contains("<script"), "html - js block");
\o\v($u_t)->u_ok(\o\v(\o\v(u_tags_html(u_ent_html()))->u_unlocked())->u_contains("<p>2 &gt; 1</p>"), "html - embed html");
$u_ls = new \o\OLockString ("<p>a &gt; c</p>");
\o\v($u_t)->u_ok(\o\v(\o\v(u_tags_html($u_ls))->u_unlocked())->u_contains("<p>a &gt; c</p>"), "html - LockString");
\o\v($u_t)->u_ok(\o\v(\o\v(u_data_js("string"))->u_unlocked())->u_contains("\"string\";"), "js - string");
\o\v($u_t)->u_ok(\o\v(\o\v(u_data_js("a\nb"))->u_unlocked())->u_contains("\"a\\nb\";"), "js - string newline");
\o\v($u_t)->u_ok(\o\v(\o\v(u_data_js("a\"b"))->u_unlocked())->u_contains("\"a\\\"b\";"), "js - string quote");
\o\v($u_t)->u_ok(\o\v(\o\v(u_data_js(1234))->u_unlocked())->u_contains("1234;"), "js - num");
\o\v($u_t)->u_ok(\o\v(\o\v(u_data_js(true))->u_unlocked())->u_contains("true;"), "js - bool");
\o\v($u_t)->u_ok(\o\v(\o\v(u_data_js(\o\OMap::create([ 'a' => 1 ])))->u_unlocked())->u_contains("{\"a\":1};"), "js - object");
 return \o\Runtime::void(__METHOD__);
}
function u_lib_file ($u_t)  {
 \o\v($u_t)->u_section("Module: File");
\o\v($u_t)->u_dies(function  ()  {
 \o\Runtime::getModule(__NAMESPACE__, 'File')->u_exists("../bad.txt");
 return \o\Runtime::void(__METHOD__);
}
, "parent shortcut (..)");
\o\v($u_t)->u_dies(function  ()  {
 \o\Runtime::getModule(__NAMESPACE__, 'File')->u_read("http://yahoo.com");
 return \o\Runtime::void(__METHOD__);
}
, "stop remote file read");
\o\v($u_t)->u_ok((! \o\Runtime::getModule(__NAMESPACE__, 'File')->u_exists("sdf/sdf")), "Missing file does not exist");
\o\v($u_t)->u_ok((! \o\Runtime::getModule(__NAMESPACE__, 'File')->u_is_file("sdf/sdf")), "Missing path is not a file");
\o\v($u_t)->u_ok((! \o\Runtime::getModule(__NAMESPACE__, 'File')->u_is_dir("sdf/sdf")), "Missing path is not a dir");
$u_f = "testFile.txt";
$u_d = "testDir";
if (\o\Runtime::getModule(__NAMESPACE__, 'File')->u_exists($u_d)) {
\o\Runtime::getModule(__NAMESPACE__, 'File')->u_delete_dir($u_d);

}

\o\Runtime::getModule(__NAMESPACE__, 'File')->u_make_dir($u_d);
\o\v($u_t)->u_ok(\o\Runtime::getModule(__NAMESPACE__, 'File')->u_is_dir($u_d), "make dir");
$u_p = \o\Runtime::getModule(__NAMESPACE__, 'File')->u_join_path(\o\OList::create([ $u_d, $u_f ]));
\o\Runtime::getModule(__NAMESPACE__, 'File')->u_write($u_p, "12345");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'File')->u_get_size($u_p) === 5), "File size");
\o\v($u_t)->u_ok(\o\Runtime::getModule(__NAMESPACE__, 'File')->u_exists($u_p), "File exists");
\o\v($u_t)->u_ok(\o\Runtime::getModule(__NAMESPACE__, 'File')->u_is_file($u_p), "File is file");
$u_info = \o\Runtime::getModule(__NAMESPACE__, 'File')->u_path_info($u_p);
\o\v($u_t)->u_ok((\o\v(\o\v($u_info)->u_dir_list)->u_last() === $u_d), "Path info dirList has parent dir");
\o\v($u_t)->u_ok((\o\v($u_info)->u_file_ext === "txt"), "Path info extension");
\o\v($u_t)->u_ok((\o\v($u_info)->u_file_name === "testFile.txt"), "Path info fileName");
\o\v($u_t)->u_ok((\o\v($u_info)->u_file_name_short === "testFile"), "Path info shortFileName");
\o\Runtime::getModule(__NAMESPACE__, 'File')->u_delete($u_p);
\o\v($u_t)->u_ok((! \o\Runtime::getModule(__NAMESPACE__, 'File')->u_exists($u_p)), "File deleted");
\o\Runtime::getModule(__NAMESPACE__, 'File')->u_delete_dir($u_d);
\o\v($u_t)->u_ok((! \o\Runtime::getModule(__NAMESPACE__, 'File')->u_exists($u_d)), "Dir deleted");
 return \o\Runtime::void(__METHOD__);
}
function u_lib_date ($u_t)  {
 \o\v($u_t)->u_section("Module: Date");
\o\v($u_t)->u_ok((\o\vn(\o\Runtime::getModule(__NAMESPACE__, 'Date')->u_now(), 0) > \o\vn(1490000000, 0)), "Date.now");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Date')->u_minutes(3) === 180), "minutes");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Date')->u_hours(2) === 7200), "hours");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Date')->u_days(3) === 259200), "days");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Date')->u_to_minutes(90) === 1.5), "inMinutes");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Date')->u_to_hours(7200) === 2), "inHours");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Date')->u_to_days(259200) === 3), "inDays");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Date')->u_format("%Y-%m-%d %H:%M:%S", 1400000000) === "2014-05-13 09:53:20"), "Date.format");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Date')->u_difference(100, 280) === "3 minutes"), "Date.difference");
 return \o\Runtime::void(__METHOD__);
}
function u_lib_db ($u_t)  {
 \o\v($u_t)->u_section("Module: Db");
\o\Runtime::getModule(__NAMESPACE__, 'Db')->u_query(new \o\OLockString ("delete from test"));
$u_key = \o\Runtime::concat("test", \o\Runtime::getModule(__NAMESPACE__, 'Math')->u_random(0, 1000));
\o\Runtime::getModule(__NAMESPACE__, 'Db')->u_insert_row("test", \o\OMap::create([ 'key' => $u_key, 'value' => \o\Runtime::getModule(__NAMESPACE__, 'Date')->u_now() ]));
$u_rows = \o\Runtime::getModule(__NAMESPACE__, 'Db')->u_select_rows(new \o\OLockString ("select * from test"));
\o\v($u_t)->u_ok((\o\v($u_rows)->u_length() === 1), "Insert & select row");
\o\v($u_t)->u_ok((\o\v(\o\v($u_rows)[0])->u_key === $u_key), "Check inserted row");
$u_dbh = \o\Runtime::getModule(__NAMESPACE__, 'Db')->u_use("default");
$u_rows = \o\v($u_dbh)->u_select_rows(new \o\OLockString ("select * from test"));
\o\v($u_t)->u_ok((\o\v(\o\v($u_rows)[0])->u_key === $u_key), "Db.use");
\o\Runtime::getModule(__NAMESPACE__, 'Db')->u_update_rows("test", \o\OMap::create([ 'key' => $u_key, 'value' => "new!" ]), \o\v(new \o\OLockString (" key = {}"))->u_fill($u_key));
$u_row = \o\Runtime::getModule(__NAMESPACE__, 'Db')->u_select_row(\o\v(new \o\OLockString ("select * from test where key = {}"))->u_fill($u_key));
\o\v($u_t)->u_ok((\o\v($u_row)->u_value === "new!"), "Update row");
\o\Runtime::getModule(__NAMESPACE__, 'Db')->u_delete_rows("test", \o\v(new \o\OLockString ("key = {}"))->u_fill($u_key));
$u_rows = \o\Runtime::getModule(__NAMESPACE__, 'Db')->u_select_rows(new \o\OLockString ("select * from test"));
\o\v($u_t)->u_ok((\o\v($u_rows)->u_length() === 0), "Delete row");
\o\v($u_t)->u_dies(function  ()  {
 \o\Runtime::getModule(__NAMESPACE__, 'Db')->u_update_rows("\"bad", \o\OMap::create([ 'key' => $u_key ]), \o\v(new \o\OLockString (" key = {}"))->u_fill($u_key));
 return \o\Runtime::void(__METHOD__);
}
, "invalid table name - updateRows");
\o\v($u_t)->u_dies(function  ()  {
 \o\Runtime::getModule(__NAMESPACE__, 'Db')->u_delete_rows("\"bad", \o\v(new \o\OLockString (" key = {}"))->u_fill($u_key));
 return \o\Runtime::void(__METHOD__);
}
, "invalid table name - deleteRows");
\o\v($u_t)->u_dies(function  ()  {
 \o\Runtime::getModule(__NAMESPACE__, 'Db')->u_query("delete from test");
 return \o\Runtime::void(__METHOD__);
}
, "reject unlocked query - query");
\o\v($u_t)->u_dies(function  ()  {
 \o\Runtime::getModule(__NAMESPACE__, 'Db')->u_select_rows("select * from test");
 return \o\Runtime::void(__METHOD__);
}
, "reject unlocked query - selectRows");
 return \o\Runtime::void(__METHOD__);
}
function u_lib_jcon_test ($u_t)  {
 \o\v($u_t)->u_section("Module: Jcon");
$u_d = \o\Runtime::getModule(__NAMESPACE__, 'Jcon')->u_parse("{\nkey: value\n}\n");
\o\v($u_t)->u_ok((\o\v($u_d)->u_key === "value"), "string value");
$u_d = \o\Runtime::getModule(__NAMESPACE__, 'Jcon')->u_parse("{\nkey: true\n}\n");
\o\v($u_t)->u_ok((\o\v($u_d)->u_key === true), "true value");
$u_d = \o\Runtime::getModule(__NAMESPACE__, 'Jcon')->u_parse("{\nkeyA: valA\nkeyB: valB\n}\n");
\o\v($u_t)->u_ok((\o\v($u_d)->u_key_b === "valB"), "2nd key");
$u_d = \o\Runtime::getModule(__NAMESPACE__, 'Jcon')->u_parse("{\nkey: false\n}\n");
\o\v($u_t)->u_ok((\o\v($u_d)->u_key === false), "false value");
$u_d = \o\Runtime::getModule(__NAMESPACE__, 'Jcon')->u_parse("{\nkey: 1234.5\n}\n");
\o\v($u_t)->u_ok((\o\v($u_d)->u_key === 1234.5), "num value");
$u_d = \o\Runtime::getModule(__NAMESPACE__, 'Jcon')->u_parse("{\nkey: [\nv1\nv2\nv3\n]\n}\n");
\o\v($u_t)->u_ok((\o\v(\o\v($u_d)->u_key)->u_length() === 3), "list value");
\o\v($u_t)->u_ok((\o\v(\o\v($u_d)->u_key)[2] === "v3"), "list value");
$u_d = \o\Runtime::getModule(__NAMESPACE__, 'Jcon')->u_parse("{\nkey: '''\nThis is\nmultiline\n'''\n}\n");
\o\v($u_t)->u_ok(\o\v(\o\v($u_d)->u_key)->u_contains("\nmultiline"), "multiline value");
$u_d = \o\Runtime::getModule(__NAMESPACE__, 'Jcon')->u_parse("{\nkeyLite: '''\n## Heading!\n'''\n}\n");
\o\v($u_t)->u_ok(\o\v(\o\v(\o\v($u_d)->u_key_lite)->u_unlocked())->u_contains("<h2>"), "Litemark value");
 return \o\Runtime::void(__METHOD__);
}
function u_lib_js ($u_t)  {
 \o\v($u_t)->u_section("Module: Js");
\o\v($u_t)->u_ok(\o\v(\o\v(\o\Runtime::getModule(__NAMESPACE__, 'Js')->u_plugin("colorCode"))->u_unlocked())->u_contains("highlight"), "colorCode");
\o\v($u_t)->u_ok(\o\v(\o\v(\o\Runtime::getModule(__NAMESPACE__, 'Js')->u_plugin("lazyLoadImages"))->u_unlocked())->u_contains("img"), "lazyLoadImages");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Js')->u_minify("/* comment */\n\nhello\n    \n") === "hello"), "minify");
 return \o\Runtime::void(__METHOD__);
}
function u_lib_json ($u_t)  {
 \o\v($u_t)->u_section("Module: Json");
\o\v($u_t)->u_ok((\o\v(\o\v(\o\Runtime::getModule(__NAMESPACE__, 'Json')->u_decode("{\"k1\":[123,\"hello\"]}"))["k1"])[1] === "hello"), "decode sub-list");
\o\v($u_t)->u_ok((\o\v(\o\v(\o\Runtime::getModule(__NAMESPACE__, 'Json')->u_decode("{\"k1\":{\"k2\":\"hello\"}}"))["k1"])["k2"] === "hello"), "decode sub-map");
\o\v($u_t)->u_ok((\o\v(\o\Runtime::getModule(__NAMESPACE__, 'Json')->u_decode("[1,2,3]"))[1] === 2), "decode list");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Json')->u_decode("true") === true), "decode boolean");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Json')->u_decode("123.45") === 123.45), "decode number");
$u_st = \o\Runtime::getModule(__NAMESPACE__, 'Json')->u_encode(\o\OMap::create([ 'a' => "hi", 'b' => \o\OList::create([ 1, 2, 3 ]) ]));
\o\v($u_t)->u_ok(\o\v($u_st)->u_contains("\"hi\""), "encode string");
\o\v($u_t)->u_ok(\o\v($u_st)->u_contains("[1,2,3]"), "encode list");
\o\v($u_t)->u_ok(\o\v($u_st)->u_contains("\"b\":"), "encode key");
$u_obj = \o\Runtime::getModule(__NAMESPACE__, 'Json')->u_decode($u_st);
\o\v($u_t)->u_ok((\o\v(\o\v($u_obj)->u_b)[1] === 2), "decode after encode");
 return \o\Runtime::void(__METHOD__);
}
function u_lib_litemark ($u_t)  {
 \o\v($u_t)->u_section("Module: Litemark");
 return \o\Runtime::void(__METHOD__);
}
function u_lib_math ($u_t)  {
 \o\v($u_t)->u_section("Module: Math");
$u_rand = \o\Runtime::getModule(__NAMESPACE__, 'Math')->u_random(6, 8);
\o\v($u_t)->u_ok(((\o\vn($u_rand, 0) >= \o\vn(6, 0)) && (\o\vn($u_rand, 0) <= \o\vn(8, 0))), "random");
$u_rnd = \o\Runtime::getModule(__NAMESPACE__, 'Math')->u_random();
\o\v($u_t)->u_ok(((\o\vn($u_rnd, 0) >= \o\vn(0, 0)) && (\o\vn($u_rnd, 0) < \o\vn(1, 0))), "random float");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Math')->u_round(\o\Runtime::getModule(__NAMESPACE__, 'Math')->u_pi(), 2) === 3.14), "rounded pi");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Math')->u_clamp(5, 1, 10) === 5), "clamp in boundary");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Math')->u_clamp(20, 1, 10) === 10), "clamp max");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Math')->u_clamp((- 20), 1, 10) === 1), "clamp min");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Math')->u_min(1, 3, 5) === 1), "min");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Math')->u_min(\o\OList::create([ 1, 3, 5 ])) === 1), "min list");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Math')->u_max(1, 3, 5) === 5), "max");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Math')->u_max(\o\OList::create([ 1, 3, 5 ])) === 5), "max list");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Math')->u_convert_base(21, 10, 2) === "10101"), "convertBase: dec to bin");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Math')->u_convert_base("1af9", 16, 10) === 6905), "convertBase: hex to dec");
 return \o\Runtime::void(__METHOD__);
}
function u_lib_meta ($u_t)  {
 \o\v($u_t)->u_section("Module: Meta");
\o\v($u_t)->u_ok(\o\Runtime::getModule(__NAMESPACE__, 'Meta')->u_function_exists("libMeta"), "functionExists");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Meta')->u_call_function("metaCallMe", \o\OList::create([ "a", "b" ])) === "a|b"), "callFunction & arguments");
\o\v($u_t)->u_ok(\o\Runtime::getModule(__NAMESPACE__, 'Meta')->u_no_template_mode(), "noTemplateMode ok");
\o\v($u_t)->u_dies(function  ()  {
 u_fail_mode_html();
 return \o\Runtime::void(__METHOD__);
}
, "noTemplateMode fail");
\o\v($u_t)->u_ok(\o\Runtime::getModule(__NAMESPACE__, 'Meta')->u_function_exists("dynamicFunction"), "dynamic function exists");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Meta')->u_call_function("dynamicFunction", \o\OList::create([ "Hey" ])) === "Hey!!!"), "call dynamic function");
 return \o\Runtime::void(__METHOD__);
}
function u_meta_call_me ()  {
 $u_args = \o\Runtime::getModule(__NAMESPACE__, 'Meta')->u_arguments();
return \o\v($u_args)->u_join("|");
 return \o\Runtime::void(__METHOD__);
}
function u_fail_template_mode ()  {
 \o\Runtime::getModule(__NAMESPACE__, 'Meta')->u_no_template_mode();
 return \o\Runtime::void(__METHOD__);
}
function u_fail_mode_html ()  {
$t = \o\Runtime::openTemplate("Html");
$t->addStatic("
    ");
u_fail_template_mode();
$t->addStatic("");
\o\Runtime::closeTemplate();
return $t->getString();
}
function u_lib_perf ($u_t)  {
 \o\v($u_t)->u_section("Module: Perf");
\o\Runtime::getModule(__NAMESPACE__, 'Perf')->u_force_active(true);
\o\Runtime::getModule(__NAMESPACE__, 'Perf')->u_start("testPerf");
\o\Runtime::getModule(__NAMESPACE__, 'System')->u_sleep(1);
\o\Runtime::getModule(__NAMESPACE__, 'Perf')->u_stop(true);
$u_res = \o\Runtime::getModule(__NAMESPACE__, 'Perf')->u_results(true);
$u_found = false;
foreach (\o\uv($u_res) as $u_r) {
if ((\o\v($u_r)->u_task === "testPerf")) {
$u_found = true;
break;

}


}
\o\v($u_t)->u_ok($u_found, "Perf task & results");
\o\Runtime::getModule(__NAMESPACE__, 'Perf')->u_force_active(false);
 return \o\Runtime::void(__METHOD__);
}
function u_lib_php ($u_t)  {
 \o\v($u_t)->u_section("Module: Php");
$u_fl = \o\Runtime::getModule(__NAMESPACE__, 'Php')->u_options(\o\OList::create([ "PATHINFO_FILENAME", "PATHINFO_BASENAME" ]));
\o\v($u_t)->u_ok(($u_fl === 10), "PHP - constant flags");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Php')->u_call(new \o\OLockString ("strrev"), \o\OList::create([ "abcdef" ])) === "fedcba"), "call");
\o\v($u_t)->u_dies(function  ()  {
 \o\Runtime::getModule(__NAMESPACE__, 'Php')->u_call(new \o\OLockString ("nonexistent"), \o\OList::create([ 1, 2 ]));
 return \o\Runtime::void(__METHOD__);
}
, "Non-existent PHP call");
\o\v($u_t)->u_dies(function  ()  {
 \o\Runtime::getModule(__NAMESPACE__, 'Php')->u_call(new \o\OLockString ("eval"), \o\OList::create([ "print(\"hi\");" ]));
 return \o\Runtime::void(__METHOD__);
}
, "stop blacklisted function - by name");
\o\v($u_t)->u_dies(function  ()  {
 \o\Runtime::getModule(__NAMESPACE__, 'Php')->u_call(new \o\OLockString ("ini_set"), \o\OList::create([ "x", "y" ]));
 return \o\Runtime::void(__METHOD__);
}
, "stop blacklisted function - by match");
 return \o\Runtime::void(__METHOD__);
}
function u_lib_test ($u_t)  {
 \o\v($u_t)->u_section("Module: Test");
 return \o\Runtime::void(__METHOD__);
}
function u_lib_global ($u_t)  {
 \o\v($u_t)->u_section("Module: Global");
u_set_globals();
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Global')->u_hello === "world"), "global set");
 return \o\Runtime::void(__METHOD__);
}
function u_set_globals ()  {
 \o\Runtime::getModule(__NAMESPACE__, 'Global')->u_hello = "world";
 return \o\Runtime::void(__METHOD__);
}
function u_lib_web ($u_t)  {
 \o\v($u_t)->u_section("Module: Web");
\o\v($u_t)->u_dies(function  ()  {
 \o\Runtime::getModule(__NAMESPACE__, 'Web')->u_redirect("http://google.com");
 return \o\Runtime::void(__METHOD__);
}
, "redirect - normal");
\o\v($u_t)->u_dies(function  ()  {
 \o\Runtime::getModule(__NAMESPACE__, 'Web')->u_redirect("mailto:google.com");
 return \o\Runtime::void(__METHOD__);
}
, "redirect - mailto");
\o\v($u_t)->u_dies(function  ()  {
 \o\Runtime::getModule(__NAMESPACE__, 'Web')->u_redirect("//google.com");
 return \o\Runtime::void(__METHOD__);
}
, "redirect - no protocol");
\o\v($u_t)->u_dies(function  ()  {
 \o\Runtime::getModule(__NAMESPACE__, 'Web')->u_redirect("bob@ftp://google.com");
 return \o\Runtime::void(__METHOD__);
}
, "redirect - ftp & username");
\o\v($u_t)->u_section("Module: Web - Form Input");
\o\v($u_t)->u_ok((u_form_validate("123") === "123"), "default token ok");
\o\v($u_t)->u_ok((u_form_validate("\$foo") === ""), "token not ok");
\o\v($u_t)->u_ok((u_form_validate("123", "id") === 123), "id ok");
\o\v($u_t)->u_ok((u_form_validate("\$foo", "id") === ""), "id not ok");
\o\v($u_t)->u_ok((u_form_validate("-123.45", "number") === (- 123.45)), "number ok");
\o\v($u_t)->u_ok((u_form_validate("\$1", "number") === ""), "number not ok");
\o\v($u_t)->u_ok((u_form_validate("true", "flag") === true), "flag ok");
\o\v($u_t)->u_ok((u_form_validate("false", "flag") === false), "flag ok");
\o\v($u_t)->u_ok((u_form_validate("1", "flag") === true), "flag ok");
\o\v($u_t)->u_ok((u_form_validate("0", "flag") === false), "flag ok");
\o\v($u_t)->u_ok((u_form_validate("\$1", "flag") === ""), "flag not ok");
\o\v($u_t)->u_ok((u_form_validate("me@mail.com", "email") === "me@mail.com"), "email ok");
\o\v($u_t)->u_ok((u_form_validate("me.com", "email") === ""), "email not ok");
\o\v($u_t)->u_ok((u_form_validate("me@mailcom", "email") === ""), "email not ok");
\o\v($u_t)->u_ok((u_form_validate("skip", "email") === ""), "email not ok");
\o\v($u_t)->u_ok((u_form_validate("abc  123!", "text") === "abc 123!"), "text ok");
\o\v($u_t)->u_ok((u_form_validate("abc<b>tag", "text") === "abctag"), "text no tag");
\o\v($u_t)->u_ok((u_form_validate("abc\nline2", "text") === "abc line2"), "text newline");
\o\v($u_t)->u_ok((u_form_validate("abc  123!", "textarea") === "abc 123!"), "textarea spaces");
\o\v($u_t)->u_ok((u_form_validate("abc<b>tag", "textarea") === "abctag"), "textarea no tag");
\o\v($u_t)->u_ok((u_form_validate("abc\n\n\nline2", "textarea") === "abc\n\nline2"), "textarea newline");
 return \o\Runtime::void(__METHOD__);
}
function u_form_validate ($u_v, $u_type="token")  {
 return \o\v(\o\Runtime::getModule(__NAMESPACE__, 'Web')->u_temp_validate_input($u_v, $u_type))["value"];
 return \o\Runtime::void(__METHOD__);
}
function u_lib_settings ($u_t)  {
 \o\v($u_t)->u_section("Module: Settings");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Global')->u_setting("num") === (- 123.45)), "get num");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Global')->u_setting("flagFalse") === false), "get flag");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Global')->u_setting("flagTrue") === true), "get flag");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Global')->u_setting("string") === "value with spaces, etc."), "get string");
\o\v($u_t)->u_ok((\o\v(\o\Runtime::getModule(__NAMESPACE__, 'Global')->u_setting("map"))->u_key === "value"), "get map");
\o\v($u_t)->u_ok((\o\v(\o\Runtime::getModule(__NAMESPACE__, 'Global')->u_setting("list"))[1] === "value 1"), "get list");
\o\v($u_t)->u_dies(function  ()  {
 \o\Runtime::getModule(__NAMESPACE__, 'Global')->u_setting("MISSING");
 return \o\Runtime::void(__METHOD__);
}
, "missing key");
 return \o\Runtime::void(__METHOD__);
}
function u_lib_map_db ($u_t)  {
 \o\v($u_t)->u_section("Module: MapDb");
\o\v($u_t)->u_ok(\o\Runtime::getModule(__NAMESPACE__, 'MapDb')->u_delete_bucket("test"), "delete bucket");
\o\v($u_t)->u_ok(\o\Runtime::getModule(__NAMESPACE__, 'MapDb')->u_insert_map("test", "hello", \o\OMap::create([ 'hello' => "World!" ])), "insert");
\o\v($u_t)->u_ok(\o\Runtime::getModule(__NAMESPACE__, 'MapDb')->u_insert_map("test", "hello", \o\OMap::create([ 'hello' => "There!" ])), "insert");
\o\v($u_t)->u_ok((\o\v(\o\Runtime::getModule(__NAMESPACE__, 'MapDb')->u_select_map("test", 1))->u_hello === "World!"), "selectMap");
\o\v($u_t)->u_ok((\o\v(\o\Runtime::getModule(__NAMESPACE__, 'MapDb')->u_select_maps("test", "hello"))->u_length() === 2), "selectMaps");
\o\v($u_t)->u_ok((\o\v(\o\v(\o\Runtime::getModule(__NAMESPACE__, 'MapDb')->u_buckets())[0])->u_num_maps === 2), "buckets()");
 return \o\Runtime::void(__METHOD__);
}
function u_lib_session ($u_t)  {
 \o\v($u_t)->u_section("Module: Session");
\o\Runtime::getModule(__NAMESPACE__, 'Session')->u_delete_all();
\o\Runtime::getModule(__NAMESPACE__, 'Session')->u_set("key1", "value");
\o\Runtime::getModule(__NAMESPACE__, 'Session')->u_set("key2", \o\OMap::create([ 'a' => "b" ]));
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Session')->u_get("key1") === "value"), "set/get");
\o\v($u_t)->u_ok((\o\v(\o\Runtime::getModule(__NAMESPACE__, 'Session')->u_get("key2"))->u_a === "b"), "get map");
\o\v($u_t)->u_ok((\o\v(\o\v(\o\Runtime::getModule(__NAMESPACE__, 'Session')->u_get_all())->u_keys())->u_join("|") === "key1|key2"), "getAll");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Session')->u_get("missing", "") === ""), "get with blank default");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Session')->u_get("missing", "default") === "default"), "get with default");
\o\v($u_t)->u_ok(\o\Runtime::getModule(__NAMESPACE__, 'Session')->u_has_key("key1"), "hasKey true");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Session')->u_delete("key1") === "value"), "delete");
\o\v($u_t)->u_ok((! \o\Runtime::getModule(__NAMESPACE__, 'Session')->u_has_key("key1")), "hasKey false");
\o\Runtime::getModule(__NAMESPACE__, 'Session')->u_delete_all();
\o\v($u_t)->u_ok((\o\v(\o\v(\o\Runtime::getModule(__NAMESPACE__, 'Session')->u_get_all())->u_keys())->u_length() === 0), "deleteAll");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Session')->u_add_counter("num") === 1), "counter 1");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Session')->u_add_counter("num") === 2), "counter 2");
\o\Runtime::getModule(__NAMESPACE__, 'Session')->u_set_flash("fkey", "fvalue");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Session')->u_get_flash("fkey") === "fvalue"), "flash set/get");
\o\v($u_t)->u_ok(\o\Runtime::getModule(__NAMESPACE__, 'Session')->u_has_flash("fkey"), "hasFlash - true");
\o\v($u_t)->u_ok(\o\Runtime::getModule(__NAMESPACE__, 'Session')->u_has_flash("missing"), "hasFlash - false");
\o\Runtime::getModule(__NAMESPACE__, 'Session')->u_add_to_list("list", 123);
\o\v($u_t)->u_ok((\o\v(\o\Runtime::getModule(__NAMESPACE__, 'Session')->u_get("list"))[0] === 123), "addToList 1");
\o\Runtime::getModule(__NAMESPACE__, 'Session')->u_add_to_list("list", 456);
\o\v($u_t)->u_ok((\o\v(\o\Runtime::getModule(__NAMESPACE__, 'Session')->u_get("list"))[1] === 456), "addToList 2");
\o\v($u_t)->u_dies(function  ()  {
 \o\Runtime::getModule(__NAMESPACE__, 'Session')->u_get("missing");
 return \o\Runtime::void(__METHOD__);
}
, "get bad key");
 return \o\Runtime::void(__METHOD__);
}
function u_lib_cache ($u_t)  {
 \o\v($u_t)->u_section("Module: Cache");
\o\Runtime::getModule(__NAMESPACE__, 'Cache')->u_set("test", 123, 1);
\o\v($u_t)->u_ok(\o\Runtime::getModule(__NAMESPACE__, 'Cache')->u_has("test"), "has");
\o\v($u_t)->u_ok((! \o\Runtime::getModule(__NAMESPACE__, 'Cache')->u_has("not")), "has not");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Cache')->u_get("test") === 123), "get");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Cache')->u_get("not", "missing") === "missing"), "get default");
\o\Runtime::getModule(__NAMESPACE__, 'Cache')->u_set("data", \o\OMap::create([ 'a' => \o\OList::create([ "x", "y", "z" ]) ]), 3);
\o\v($u_t)->u_ok((\o\v(\o\v(\o\Runtime::getModule(__NAMESPACE__, 'Cache')->u_get("data"))->u_a)->u_join("|") === "x|y|z"), "get map + list");
\o\Runtime::getModule(__NAMESPACE__, 'Cache')->u_delete("data");
\o\v($u_t)->u_ok((! \o\Runtime::getModule(__NAMESPACE__, 'Cache')->u_has("data")), "delete");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Cache')->u_counter("count") === 1), "counter 1");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Cache')->u_counter("count") === 2), "counter 2");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Cache')->u_counter("count", 2) === 4), "counter +2");
\o\v($u_t)->u_ok((\o\Runtime::getModule(__NAMESPACE__, 'Cache')->u_counter("count", (- 1)) === 3), "counter -1");
\o\Runtime::getModule(__NAMESPACE__, 'Cache')->u_delete("count");
\o\Runtime::getModule(__NAMESPACE__, 'Cache')->u_set("short", "a", 0.1);
\o\Runtime::getModule(__NAMESPACE__, 'Cache')->u_set("longer", "a", 0.5);
\o\Runtime::getModule(__NAMESPACE__, 'Cache')->u_set("forever", "a", 0);
\o\Runtime::getModule(__NAMESPACE__, 'System')->u_sleep(200);
\o\v($u_t)->u_ok((! \o\Runtime::getModule(__NAMESPACE__, 'Cache')->u_has("short")), "100ms expiry");
\o\v($u_t)->u_ok(\o\Runtime::getModule(__NAMESPACE__, 'Cache')->u_has("longer"), "500ms expiry");
\o\v($u_t)->u_ok(\o\Runtime::getModule(__NAMESPACE__, 'Cache')->u_has("forever"), "no expiry");
\o\Runtime::getModule(__NAMESPACE__, 'Cache')->u_delete("short");
\o\Runtime::getModule(__NAMESPACE__, 'Cache')->u_delete("longer");
\o\Runtime::getModule(__NAMESPACE__, 'Cache')->u_delete("forever");
 return \o\Runtime::void(__METHOD__);
}
function u_template_html ($u_users)  {
$t = \o\Runtime::openTemplate("Html");
$t->addStatic("<b>Hello</b>");
foreach (\o\uv($u_users) as $u_u) {
$t->addStatic("<li>");
$t->addDynamic($u_u);
$t->addStatic("</li>");

}
$t->addStatic("
");
\o\Runtime::closeTemplate();
return $t->getString();
}
function u_data_js ($u_d)  {
$t = \o\Runtime::openTemplate("Js");
$t->addStatic("let d=");
$t->addDynamic($u_d);
$t->addStatic(";");
\o\Runtime::closeTemplate();
return $t->getString();
}
function u_ent_html ()  {
$t = \o\Runtime::openTemplate("Html");
$t->addStatic("<p>2 &gt; 1</p>");
\o\Runtime::closeTemplate();
return $t->getString();
}
function u_format_block_html ()  {
$t = \o\Runtime::openTemplate("Html");
$t->addStatic("<p>&lt;foo&gt;</p>");
\o\Runtime::closeTemplate();
return $t->getString();
}
function u_bs_html ()  {
$t = \o\Runtime::openTemplate("Html");
$t->addStatic("
    a\\nb\\nc
");
\o\Runtime::closeTemplate();
return $t->getString();
}
function u_js_html ()  {
$t = \o\Runtime::openTemplate("Html");
$t->addStatic("<script nonce=\"");
$t->addDynamic(\o\Runtime::getModule(__NAMESPACE__, 'Web')->u_nonce());
$t->addStatic("\">var a = '&lt;a\\nb\\nc';</script>");
\o\Runtime::closeTemplate();
return $t->getString();
}
function u_exp_html ($u_inner, $u_outer)  {
$t = \o\Runtime::openTemplate("Html");
$t->addStatic("<p ");
$t->addDynamic($u_inner);
$t->addStatic(">");
$t->addDynamic($u_outer);
$t->addStatic("</p>");
\o\Runtime::closeTemplate();
return $t->getString();
}
function u_tags_html ($u_exp)  {
$t = \o\Runtime::openTemplate("Html");
$t->addStatic("
    ");
$t->addDynamic($u_exp);
$t->addStatic("
");
\o\Runtime::closeTemplate();
return $t->getString();
}
function u_in_css ()  {
$t = \o\Runtime::openTemplate("Css");
$t->addStatic("font-weight:bold;");
\o\Runtime::closeTemplate();
return $t->getString();
}
function u_in_js ()  {
$t = \o\Runtime::openTemplate("Js");
$t->addStatic("var a=1;");
\o\Runtime::closeTemplate();
return $t->getString();
}
function u_lock_html ($u_lock)  {
$t = \o\Runtime::openTemplate("Html");
$t->addStatic("<p>");
$t->addDynamic($u_lock);
$t->addStatic("</p>");
\o\Runtime::closeTemplate();
return $t->getString();
}
function u_exp_css ($u_inp)  {
$t = \o\Runtime::openTemplate("Css");
$t->addStatic("font-weight:");
$t->addDynamic($u_inp);
$t->addStatic(";");
\o\Runtime::closeTemplate();
return $t->getString();
}
function u_dynamic_function ($u_a)  {
 return \o\Runtime::concat($u_a, "!!!");
 return \o\Runtime::void(__METHOD__);
}
function u_no_return ()  {
  return \o\Runtime::void(__METHOD__);
}



/* SOURCE={"file":"pages\/home.tht","6":4,"7":6,"8":7,"9":9,"12":12,"14":18,"15":18,"16":26,"17":26,"18":32,"22":36,"23":38,"24":39,"25":40,"26":41,"27":42,"28":43,"29":44,"30":45,"31":46,"32":48,"33":49,"34":51,"35":52,"36":53,"37":54,"38":55,"39":56,"40":57,"41":58,"42":60,"43":61,"44":62,"45":63,"46":64,"47":66,"48":67,"51":71,"52":73,"53":75,"54":75,"57":75,"58":76,"59":76,"62":76,"63":78,"64":79,"67":80,"68":82,"69":83,"72":84,"73":86,"74":87,"75":87,"80":90,"81":92,"82":92,"85":92,"88":96,"89":98,"90":100,"97":111,"98":112,"99":113,"100":114,"101":115,"102":118,"103":120,"104":121,"105":122,"106":123,"107":124,"108":125,"109":126,"110":127,"111":128,"112":131,"113":133,"114":134,"115":135,"116":136,"117":139,"118":141,"119":142,"120":143,"121":144,"122":145,"123":146,"124":147,"125":148,"126":149,"127":150,"128":151,"129":152,"130":153,"131":154,"132":155,"133":156,"134":157,"135":158,"136":159,"137":160,"138":161,"139":162,"140":163,"141":164,"142":165,"143":166,"144":167,"145":168,"146":169,"147":170,"148":171,"149":182,"150":184,"151":185,"152":186,"153":187,"154":188,"155":189,"156":190,"157":191,"158":192,"159":195,"160":197,"161":198,"162":199,"163":200,"164":201,"165":202,"166":206,"167":208,"168":209,"169":210,"170":211,"171":212,"172":213,"173":214,"174":215,"175":216,"176":217,"177":218,"178":219,"179":220,"180":221,"181":222,"182":223,"183":224,"184":225,"185":226,"186":227,"187":228,"188":229,"189":230,"190":231,"191":232,"192":233,"193":234,"194":235,"195":236,"196":237,"197":238,"198":239,"199":240,"200":241,"201":242,"202":244,"203":245,"204":246,"205":247,"206":248,"207":251,"208":253,"209":254,"210":255,"211":256,"212":257,"213":258,"214":260,"215":261,"216":262,"217":263,"218":264,"219":267,"220":269,"221":270,"222":271,"223":272,"224":273,"225":274,"226":275,"227":276,"228":279,"229":281,"230":282,"231":283,"232":284,"233":285,"234":286,"235":287,"236":288,"237":289,"238":290,"239":291,"240":292,"241":293,"242":294,"243":295,"244":296,"245":297,"246":298,"247":300,"250":308,"251":310,"252":313,"253":314,"254":315,"255":316,"256":317,"257":318,"258":319,"259":320,"262":322,"263":323,"264":324,"265":325,"266":328,"267":334,"268":336,"269":337,"270":338,"271":340,"272":341,"273":342,"274":347,"275":349,"276":355,"277":356,"278":358,"279":359,"282":363,"283":365,"284":367,"285":368,"286":369,"287":370,"288":371,"289":372,"290":373,"291":374,"292":374,"295":375,"296":379,"297":381,"298":382,"299":383,"300":384,"301":385,"302":386,"303":387,"304":389,"305":390,"306":391,"307":392,"308":393,"309":394,"310":395,"313":400,"314":402,"315":404,"316":405,"319":407,"320":409,"321":410,"324":412,"325":414,"326":415,"329":417,"330":419,"331":420,"332":421,"333":422,"336":424,"339":426,"340":427,"341":432,"342":433,"345":435,"346":436,"347":438,"348":439,"349":441,"350":442,"351":443,"355":445,"356":448,"357":449,"360":451,"361":452,"362":453,"363":455,"364":456,"365":460,"366":461,"369":463,"370":464,"371":465,"372":468,"373":469,"374":470,"378":472,"379":475,"380":475,"382":476,"383":476,"386":476,"387":477,"388":477,"391":477,"392":483,"393":485,"394":486,"395":487,"396":488,"397":489,"398":491,"399":494,"400":494,"403":494,"404":496,"405":496,"408":496,"409":497,"410":497,"413":497,"414":498,"415":498,"418":498,"419":499,"420":499,"423":499,"424":501,"425":502,"426":502,"429":502,"430":504,"431":504,"434":504,"435":505,"436":505,"439":505,"440":506,"441":506,"444":506,"447":512,"448":514,"449":516,"450":517,"451":518,"452":519,"453":520,"454":521,"455":522,"456":523,"457":524,"458":525,"459":526,"460":527,"461":530,"462":532,"463":534,"464":535,"465":540,"466":542,"467":543,"468":543,"471":543,"472":546,"473":548,"474":549,"475":550,"476":551,"477":552,"478":553,"479":556,"480":558,"481":559,"482":560,"483":562,"484":563,"485":564,"486":567,"487":569,"488":571,"489":572,"490":574,"491":575,"492":577,"493":578,"494":581,"495":582,"496":583,"497":584,"498":585,"499":586,"500":587,"501":590,"502":591,"503":591,"506":591,"507":592,"508":595,"509":596,"510":597,"511":598,"512":599,"513":602,"514":604,"515":604,"518":604,"519":605,"520":605,"523":605,"526":610,"527":613,"528":615,"529":616,"530":617,"531":618,"532":619,"533":620,"534":621,"535":622,"536":624,"537":625,"538":626,"539":627,"540":628,"541":629,"542":630,"543":632,"544":633,"545":635,"546":638,"547":640,"548":640,"551":640,"552":641,"553":641,"556":641,"557":642,"558":642,"561":642,"562":643,"563":643,"566":643,"567":644,"568":644,"571":644,"572":645,"573":645,"576":645,"577":646,"578":646,"579":646,"582":646,"583":647,"584":647,"587":647,"588":648,"589":648,"592":648,"593":649,"594":649,"597":649,"598":650,"599":650,"602":650,"603":651,"604":651,"607":651,"608":652,"609":652,"612":652,"613":655,"614":657,"615":658,"616":659,"617":660,"618":661,"619":662,"620":665,"621":667,"622":668,"623":669,"624":670,"625":673,"626":675,"627":676,"628":677,"629":678,"630":679,"631":680,"632":681,"633":682,"634":684,"635":685,"636":686,"637":687,"638":689,"639":690,"640":691,"641":692,"642":695,"643":697,"644":698,"645":699,"646":700,"647":701,"648":702,"649":703,"650":704,"651":705,"652":706,"653":707,"654":710,"655":712,"656":713,"657":714,"658":715,"659":716,"660":718,"661":720,"662":721,"663":722,"664":723,"667":727,"668":730,"669":732,"670":733,"671":734,"674":736,"675":738,"676":739,"677":740,"680":742,"681":744,"682":745,"683":746,"684":747,"687":749,"688":752,"689":753,"690":754,"691":755,"692":756,"693":757,"694":757,"700":759,"701":761,"702":762,"703":763,"704":764,"705":765,"706":766,"710":768,"711":769,"712":770,"718":773,"719":775,"720":776,"721":779,"722":780,"723":781,"724":782,"725":783,"726":784,"727":785,"728":786,"729":787,"730":788,"731":789,"732":790,"733":791,"734":792,"735":793,"736":794,"737":795,"738":796,"739":797,"740":798,"741":799,"742":800,"743":801,"744":802,"745":803,"746":804,"747":805,"748":806,"749":809,"750":811,"751":813,"752":813,"756":814,"757":816,"758":816,"762":817,"763":819,"764":820,"768":823,"772":825,"773":827,"774":828,"777":830,"778":831,"783":833,"784":835,"785":836,"788":838,"789":839,"792":841,"793":842,"797":845,"803":847,"804":851,"805":853,"806":854,"807":855,"808":856,"811":858,"812":859,"816":862,"820":865,"821":866,"822":868,"823":869,"824":870,"827":871,"828":872,"832":874,"833":877,"834":879,"835":880,"838":884,"839":886,"840":888,"841":889,"842":890,"845":895,"846":898,"847":900,"848":901,"849":902,"850":903,"851":904,"852":906,"853":907,"854":908,"855":911,"856":913,"857":914,"858":915,"859":916,"860":917,"861":918,"862":919,"863":920,"864":921,"865":922,"866":925,"867":926,"868":927,"869":928,"870":930,"871":931,"872":934,"873":936,"874":937,"875":939,"876":940,"877":942,"878":943,"879":944,"880":946,"881":947,"882":949,"883":950,"884":952,"885":953,"886":954,"887":955,"888":957,"889":958,"890":959,"891":961,"892":962,"893":963,"894":965,"895":966,"896":968,"897":969,"898":971,"899":972,"900":973,"901":974,"902":975,"903":977,"904":978,"905":979,"906":980,"907":981,"908":982,"909":984,"910":985,"911":987,"912":988,"913":990,"914":991,"915":993,"916":994,"917":996,"918":997,"919":998,"920":1000,"921":1001,"922":1002,"923":1003,"924":1005,"925":1006,"926":1008,"927":1009,"928":1010,"929":1011,"930":1012,"931":1013,"932":1015,"933":1017,"937":1025,"938":1026,"939":1027,"940":1029,"941":1030,"942":1032,"943":1033,"944":1035,"945":1037,"946":1038,"947":1039,"948":1040,"949":1042,"950":1043,"951":1044,"952":1046,"953":1047,"954":1048,"955":1050,"956":1051,"957":1052,"958":1059,"959":1060,"960":1062,"961":1063,"962":1064,"963":1065,"964":1068,"965":1070,"966":1071,"967":1072,"968":1074,"969":1075,"970":1076,"971":1078,"972":1079,"973":1080,"974":1081,"975":1083,"976":1084,"977":1085,"978":1086,"979":1088,"980":1089,"981":1090,"982":1091,"983":1093,"984":1094,"985":1095,"986":1096,"987":1098,"988":1099,"989":1100,"990":1101,"991":1102,"992":1104,"993":1105,"994":1106,"995":1107,"996":1109,"997":1110,"998":1111,"999":1112,"1000":1113,"1001":1115,"1002":1116,"1003":1117,"1004":1118,"1005":1119,"1006":1121,"1007":1122,"1008":1123,"1009":1124,"1010":1125,"1011":1126,"1012":1127,"1013":1129,"1014":1130,"1015":1131,"1016":1132,"1017":1135,"1018":1137,"1019":1138,"1020":1139,"1021":1140,"1022":1141,"1023":1144,"1024":1145,"1025":1146,"1026":1149,"1027":1151,"1028":1152,"1029":1153,"1030":1153,"1033":1153,"1034":1155,"1035":1156,"1036":1158,"1037":1159,"1038":1161,"1039":1162,"1040":1164,"1041":1165,"1042":1167,"1043":1169,"1044":1170,"1045":1173,"1046":1175,"1047":1176,"1048":1178,"1049":1178,"1052":1178,"1053":1179,"1054":1179,"1057":1179,"1058":1181,"1059":1182,"1060":1183,"1061":1184,"1062":1185,"1063":1187,"1064":1188,"1067":1192,"1068":1194,"1069":1196,"1070":1197,"1071":1198,"1072":1199,"1073":1200,"1074":1201,"1075":1202,"1076":1203,"1077":1204,"1078":1205,"1079":1206,"1080":1208,"1081":1209,"1082":1214,"1083":1217,"1084":1222,"1085":1223,"1086":1224,"1087":1225,"1088":1227,"1089":1230,"1090":1231,"1091":1232,"1092":1233,"1093":1234,"1094":1236,"1095":1237,"1096":1238,"1097":1239,"1098":1241,"1099":1242,"1100":1244,"1101":1245,"1102":1246,"1103":1250,"1104":1251,"1105":1252,"1106":1256,"1107":1257,"1108":1258,"1109":1262,"1110":1263,"1111":1267,"1112":1270,"1113":1271,"1114":1273,"1115":1273,"1119":1274,"1120":1276,"1121":1277,"1122":1279,"1123":1280,"1124":1282,"1125":1283,"1126":1285,"1127":1286,"1128":1288,"1129":1288,"1132":1288,"1133":1290,"1134":1291,"1135":1294,"1136":1296,"1137":1296,"1140":1296,"1141":1297,"1142":1297,"1145":1297,"1146":1298,"1147":1298,"1150":1298,"1151":1299,"1152":1299,"1155":1299,"1156":1300,"1157":1300,"1160":1300,"1163":1305,"1164":1307,"1165":1309,"1166":1310,"1167":1311,"1168":1312,"1169":1314,"1171":1318,"1172":1319,"1173":1320,"1174":1324,"1175":1326,"1176":1327,"1177":1329,"1178":1330,"1179":1331,"1180":1333,"1181":1334,"1182":1335,"1183":1337,"1184":1338,"1185":1341,"1186":1342,"1187":1343,"1188":1344,"1189":1345,"1190":1346,"1193":1354,"1194":1356,"1195":1358,"1196":1358,"1199":1358,"1200":1359,"1201":1359,"1204":1359,"1205":1361,"1206":1362,"1207":1363,"1208":1365,"1209":1366,"1210":1368,"1211":1369,"1215":1371,"1216":1372,"1217":1374,"1218":1376,"1219":1377,"1220":1378,"1221":1379,"1222":1381,"1223":1382,"1224":1383,"1225":1384,"1226":1385,"1227":1386,"1228":1387,"1229":1389,"1230":1390,"1233":1395,"1234":1397,"1235":1399,"1236":1400,"1237":1401,"1238":1402,"1239":1403,"1240":1404,"1241":1405,"1242":1406,"1243":1407,"1246":1410,"1247":1412,"1248":1414,"1249":1416,"1250":1417,"1251":1419,"1252":1420,"1253":1421,"1254":1423,"1255":1424,"1256":1425,"1257":1427,"1258":1428,"1259":1429,"1260":1431,"1261":1432,"1262":1433,"1263":1435,"1264":1436,"1267":1437,"1268":1439,"1269":1440,"1272":1441,"1273":1443,"1274":1444,"1277":1445,"1278":1447,"1279":1448,"1282":1449,"1285":1452,"1286":1454,"1287":1456,"1288":1457,"1289":1459,"1290":1460,"1291":1462,"1292":1463,"1293":1465,"1294":1466,"1295":1468,"1296":1469,"1297":1471,"1298":1472,"1299":1473,"1300":1475,"1301":1476,"1302":1478,"1303":1479,"1306":1482,"1307":1484,"1308":1486,"1309":1487,"1310":1488,"1313":1491,"1314":1493,"1315":1495,"1316":1496,"1317":1497,"1318":1498,"1319":1499,"1320":1501,"1321":1502,"1322":1503,"1323":1504,"1324":1506,"1325":1507,"1328":1510,"1329":1512,"1332":1516,"1333":1518,"1334":1520,"1335":1521,"1336":1523,"1337":1524,"1338":1526,"1339":1528,"1340":1529,"1341":1530,"1342":1532,"1343":1533,"1344":1535,"1345":1536,"1346":1538,"1347":1539,"1350":1543,"1351":1544,"1352":1546,"1353":1547,"1354":1548,"1355":1549,"1356":1549,"1359":1549,"1360":1551,"1361":1552,"1364":1555,"1365":1556,"1366":1557,"1369":1560,"1370":1561,"1373":1564,"1375":1565,"1377":1565,"1378":1566,"1382":1569,"1383":1570,"1384":1572,"1385":1573,"1386":1574,"1387":1575,"1388":1577,"1389":1578,"1390":1579,"1391":1580,"1392":1581,"1393":1582,"1399":1585,"1400":1587,"1403":1590,"1404":1592,"1405":1594,"1406":1595,"1407":1597,"1408":1598,"1409":1598,"1412":1598,"1413":1599,"1414":1599,"1417":1599,"1418":1600,"1419":1600,"1422":1600,"1425":1610,"1426":1611,"1429":1614,"1430":1615,"1431":1617,"1432":1619,"1435":1622,"1436":1623,"1439":1627,"1440":1629,"1441":1631,"1442":1631,"1445":1631,"1446":1632,"1447":1632,"1450":1632,"1451":1633,"1452":1633,"1455":1633,"1456":1634,"1457":1634,"1460":1634,"1461":1636,"1462":1638,"1463":1639,"1464":1641,"1465":1642,"1466":1644,"1467":1645,"1468":1647,"1469":1648,"1470":1649,"1471":1650,"1472":1651,"1473":1653,"1474":1654,"1475":1655,"1476":1656,"1477":1658,"1478":1659,"1479":1660,"1480":1662,"1481":1663,"1482":1664,"1485":1668,"1486":1669,"1489":1672,"1490":1674,"1491":1676,"1492":1677,"1493":1678,"1494":1679,"1495":1680,"1496":1681,"1497":1683,"1498":1683,"1501":1683,"1504":1686,"1505":1688,"1506":1690,"1507":1692,"1508":1693,"1509":1696,"1510":1697,"1511":1698,"1514":1703,"1515":1705,"1516":1707,"1517":1709,"1518":1710,"1519":1711,"1520":1712,"1521":1714,"1522":1716,"1523":1717,"1524":1719,"1525":1720,"1526":1721,"1527":1723,"1528":1724,"1529":1726,"1530":1727,"1531":1729,"1532":1730,"1533":1732,"1534":1733,"1535":1735,"1536":1736,"1537":1738,"1538":1739,"1539":1741,"1540":1741,"1543":1741,"1546":1745,"1547":1747,"1548":1749,"1549":1750,"1550":1752,"1551":1753,"1552":1755,"1553":1757,"1554":1758,"1555":1760,"1556":1761,"1557":1763,"1558":1764,"1559":1765,"1560":1766,"1561":1768,"1562":1771,"1563":1772,"1564":1773,"1565":1774,"1566":1775,"1567":1776,"1568":1777,"1569":1779,"1570":1780,"1571":1781,"1574":1788,"1576":1791,"1577":1791,"1578":1792,"1579":1792,"1580":1793,"1583":1795,"1588":1797,"1590":1798,"1591":1798,"1592":1799,"1596":1801,"1598":1803,"1602":1805,"1604":1809,"1608":1811,"1610":1813,"1616":1815,"1618":1816,"1619":1816,"1620":1819,"1624":1821,"1626":1822,"1627":1822,"1628":1822,"1629":1822,"1630":1823,"1634":1825,"1636":1826,"1638":1826,"1639":1827,"1644":1829,"1646":1831,"1650":1833,"1652":1835,"1656":1837,"1658":1838,"1659":1838,"1660":1839,"1664":1841,"1666":1842,"1667":1842,"1668":1843,"1672":1849,"1673":1850,"1676":1853,"1677":1855} */

?>