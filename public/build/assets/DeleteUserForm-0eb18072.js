import{o as m,c as b,s as f,z as v,J as h,l as w,i as g,d as e,t as u,j as $,u as d,T as k,r as _,v as M,a as c,w as y,b as E,K as S}from"./app-ddd1e4e5.js";import{_ as x}from"./_plugin-vue_export-helper-c27b6911.js";import{_ as C}from"./InputError-e7727c9c.js";import{_ as B}from"./InputLabel-a336c410.js";import{_ as D}from"./TextInput-3aa54041.js";const T={},V={type:"button",class:"btn btn-danger"};function N(o,r){return m(),b("button",V,[f(o.$slots,"default")])}const A=x(T,[["render",N]]),I=["id"],K={class:"modal-dialog"},L={class:"modal-content"},O={class:"modal-header"},U={class:"modal-title fs-5",id:"vueModalLabel"},Y={class:"modal-body"},P={class:"modal-footer"},j={type:"button",class:"btn btn-secondary","data-bs-dismiss":"modal"},q=["disabled"],z={__name:"Modal",props:{id:{type:String,required:!0},title:{type:String,required:!0},form:{type:Object},buttonYes:{type:String,default:"Save"},buttonNo:{type:String,default:"Close"},buttonType:{type:String,default:"primary"}},emits:["yesEvent"],setup(o,{expose:r}){const n=o;v(()=>document.addEventListener("keydown",i)),h(()=>{document.removeEventListener("keydown",i)});const t=w(()=>"btn-"+n.buttonType),l=()=>{var s=document.getElementById(n.id),a=bootstrap.Modal.getInstance(s);a.hide()},i=s=>{s.key==="Escape"&&n.show&&l()};return r({close:l}),(s,a)=>(m(),g(k,{to:"body"},[e("div",{id:o.id,class:"modal fade",tabindex:"-1","aria-labelledby":"vueModalLabel","aria-hidden":"true"},[e("div",K,[e("div",L,[e("div",O,[e("h1",U,u(o.title),1),e("button",{type:"button",class:"btn-close",onClick:l,"aria-label":"Close"})]),e("div",Y,[f(s.$slots,"default")]),e("div",P,[e("button",j,u(o.buttonNo),1),e("button",{type:"button",class:$(["btn",d(t)]),onClick:a[0]||(a[0]=p=>s.$emit("yesEvent")),disabled:o.form.processing},u(o.buttonYes),11,q)])])])],8,I)]))}},F={class:"col-lg-6 p-3"},J=e("header",null,[e("h2",null,"Delete Account"),e("p",{class:"text-muted"}," Once your account is deleted, all of its resources and data will be permanently deleted. ")],-1),G=e("p",{class:"text-danger"}," Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account. ",-1),H={class:"mt-6"},ee={__name:"DeleteUserForm",setup(o){const r=_(null),n=_(null),t=M({password:""}),l=()=>{t.delete(route("profile.destroy"),{preserveScroll:!0,onSuccess:()=>i(),onError:()=>r.value.focus(),onFinish:()=>t.reset()})},i=()=>{n.value.close(),t.reset()};return(s,a)=>(m(),b("section",F,[J,c(A,{type:"button",class:"btn btn-primary","data-bs-toggle":"modal","data-bs-target":"#deleteModal"},{default:y(()=>[E("Delete Account")]),_:1}),c(z,{ref_key:"deleteModal",ref:n,onYesEvent:l,id:"deleteModal",title:" Are you sure you want to delete your account?",buttonYes:"Delete Account",buttonType:"danger",form:d(t)},{default:y(()=>[G,e("div",H,[c(B,{for:"delete_password",value:"Password",class:"sr-only"}),c(D,{id:"delete_password",ref_key:"passwordInput",ref:r,modelValue:d(t).password,"onUpdate:modelValue":a[0]||(a[0]=p=>d(t).password=p),invalid:d(t).errors.password,type:"password",placeholder:"Password",onKeyup:S(l,["enter"])},null,8,["modelValue","invalid","onKeyup"]),c(C,{message:d(t).errors.password,class:"mt-2"},null,8,["message"])])]),_:1},8,["form"])]))}};export{ee as default};
