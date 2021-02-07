FROM node:12 as node

RUN mkdir /app
WORKDIR /app

ENV PATH /app/node_modules/.bin:$PATH

COPY package.json /app/package.json

RUN npm install

RUN npm install -g @angular/cli

COPY . /app

CMD ng build --prod

FROM nginx:alpine
RUN mkdir /app
COPY --from=node /app/dist /app/dist
COPY .docker/nginx/default.conf /etc/nginx/conf.d/default.conf