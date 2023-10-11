import{r as x,v as C,c as f,a as l,u as o,w as r,F as m,o as c,X as S,b as i,t as u,d as t,g as B,e as O,f as j,n as h,h as y,i as D,O as I}from"./app-ddd1e4e5.js";import{P as N}from"./PrimaryButton-27973225.js";import{H as v}from"./HeadRow-512d298a.js";import{_ as V}from"./Paginate-3b2c713c.js";import{A}from"./AuthenticatedLayout-c36b5e05.js";import{f as T}from"./helper-449b5e06.js";import"./_plugin-vue_export-helper-c27b6911.js";const $={class:"my-3 p-3 bg-body rounded shadow-sm"},E=["onSubmit"],F={class:"row mb-3"},H={class:"col-md-3"},K={class:"form-floating mb-3"},L=t("label",{for:"keywordInput"},"Keyword",-1),M={class:"col-12"},P=t("i",{class:"bi bi-search"},null,-1),G={class:"d-grid gap-2 d-md-flex justify-content-md-end mb-3"},R=t("i",{class:"bi bi-plus"},null,-1),U=t("i",{class:"bi bi-filetype-csv"},null,-1),X={class:"table table-bordered table-striped table-hover"},q={width:"10%"},z=t("i",{class:"bi bi-pencil"},null,-1),J=["onClick"],Q=t("i",{class:"bi bi-trash"},null,-1),W=[Q],it={__name:"Index",props:{header:{type:Object},filters:{type:Object},list:{type:Object,default:()=>({})}},setup(a){const w=a,d="costcenters",_=x("Cost Centers"),s=C(w.filters),k=n=>{s.sort.field=n,s.sort.direction=s.sort.direction==""||s.sort.direction=="desc"?"asc":"desc",p()},p=()=>{s.get(route(d+".index"),{preserveScroll:!0})},g=(n,b)=>{confirm(`Delete this cost center ${b} ?`)&&I.delete(route(d+".destroy",n))};return(n,b)=>(c(),f(m,null,[l(o(S),{title:_.value},null,8,["title"]),l(A,null,{header:r(()=>[i(u(_.value),1)]),default:r(()=>[t("div",$,[t("form",{onSubmit:B(p,["prevent"])},[t("div",F,[t("div",H,[t("div",K,[O(t("input",{"onUpdate:modelValue":b[0]||(b[0]=e=>o(s).keyword=e),type:"text",class:"form-control",id:"keywordInput",placeholder:"Keyword",autocomplete:"off"},null,512),[[j,o(s).keyword]]),L])]),t("div",M,[l(N,{type:"submit",disabled:o(s).processing},{default:r(()=>[P,i(" Search ")]),_:1},8,["disabled"])])])],40,E),t("div",G,[l(o(h),{class:"btn btn-outline-primary btn-sm",href:n.route(d+".create")},{default:r(()=>[R,i(" Create ")]),_:1},8,["href"]),l(o(h),{class:"btn btn-outline-primary btn-sm",href:n.route(d+".import")},{default:r(()=>[U,i(" Import ")]),_:1},8,["href"])]),t("table",X,[t("thead",null,[t("tr",null,[l(v,null,{default:r(()=>[i("Actions")]),_:1}),(c(!0),f(m,null,y(a.header,e=>(c(),D(v,{field:e.field,sort:e.sortable?a.filters.sort:null,onSortEvent:k,disabled:o(s).processing},{default:r(()=>[i(u(e.title),1)]),_:2},1032,["field","sort","disabled"]))),256))])]),t("tbody",null,[(c(!0),f(m,null,y(a.list.data,(e,Y)=>(c(),f("tr",null,[t("td",q,[l(o(h),{href:n.route(d+".edit",e.id),class:"btn btn-sm btn-link"},{default:r(()=>[z]),_:2},1032,["href"]),t("button",{onClick:Z=>g(e.id,e.code),class:"btn btn-sm btn-link"},W,8,J)]),t("td",null,u(e.code),1),t("td",null,u(e.name),1),t("td",null,u(o(T)(e.created_at)),1)]))),256))])]),l(V,{data:a.list},null,8,["data"])])]),_:1})],64))}};export{it as default};
