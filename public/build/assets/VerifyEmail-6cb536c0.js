import{v as m,l as f,i as p,w as t,o as a,a as s,u as e,X as _,c as h,p as g,d as i,b as n,n as b,g as y}from"./app-ddd1e4e5.js";import{_ as v,G as k}from"./GuestPrimaryButton-141538d7.js";import"./_plugin-vue_export-helper-c27b6911.js";const w=i("div",{class:"mb-4"},[i("small",null,"Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.")],-1),V={key:0,class:"mb-4 text-success"},B=["onSubmit"],C={__name:"VerifyEmail",props:{status:{type:String}},setup(r){const c=r,o=m({}),l=()=>{o.post(route("verification.send"))},d=f(()=>c.status==="verification-link-sent");return(u,x)=>(a(),p(v,null,{default:t(()=>[s(e(_),{title:"Email Verification"}),w,e(d)?(a(),h("div",V," A new verification link has been sent to the email address you provided during registration. ")):g("",!0),i("form",{onSubmit:y(l,["prevent"])},[s(k,{disabled:e(o).processing},{default:t(()=>[n(" Resend Verification Email ")]),_:1},8,["disabled"]),s(e(b),{href:u.route("logout"),method:"post",as:"button",class:"mt-4 w-50 btn btn-warning"},{default:t(()=>[n("Log Out")]),_:1},8,["href"])],40,B)]),_:1}))}};export{C as default};
